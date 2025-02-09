<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskProposal;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConversationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Stripe\StripeClient;
use Psr\Log\LoggerInterface;

class TaskProposalController extends AbstractController
{
    #[Route('/offer/make/{id}', name: 'app_make_offer', methods: ['POST'])]
    public function makeOffer(Request $request, Task $task, EntityManagerInterface $em, ConversationRepository $conversationRepository): Response
    {
        $proposedPrice = $request->request->get('proposedPrice');
        if ($proposedPrice > 999999) {
            $this->addFlash('error', 'Le prix proposé ne peut pas dépasser 999 999 €.');
            return $this->redirectToRoute('app_default');
        }

        $this->denyAccessUnlessGranted('ROLE_AGENT');

        $proposedPrice = $request->request->get('proposedPrice');
        if (!$proposedPrice) {
            $this->addFlash('error', 'Veuillez indiquer un prix valide.');
            return $this->redirectToRoute('app_default');
        }

        $user = $this->getUser();
        $agent = $user->getIdAgent();
        if (!$agent) {
            throw $this->createNotFoundException('Agent non trouvé.');
        }

        $customerUser = $task->getOwner();
        $customer = $customerUser->getIdCustomer();
        if (!$customer) {
            throw $this->createNotFoundException('Customer non trouvé.');
        }

        $conversation = $conversationRepository->findOneBy([
            'idAgent'    => $agent,
            'idCustomer' => $customer,
        ]);

        if (!$conversation) {
            $conversation = new Conversation();
            $conversation->setStartedAt(new \DateTimeImmutable());
            $conversation->setIdAgent($agent);
            $conversation->setIdCustomer($customer);
            $em->persist($conversation);
        }

        $taskProposal = new TaskProposal();
        $taskProposal->setProposedPrice($proposedPrice);
        $taskProposal->setStatus('pending');
        $taskProposal->setAgent($agent);
        $taskProposal->setTask($task);
        $em->persist($taskProposal);

        $message = new Message();
        $message->setContent("Offre de " . $proposedPrice . "€ pour la tâche : " . $task->getTitle());
        $message->setSentAt(new \DateTimeImmutable());
        $message->setIdConversation($conversation);
        $message->setIdUser($user);
        $message->setTaskProposal($taskProposal);
        $em->persist($message);

        $em->flush();

        $this->addFlash('success', 'Votre offre a bien été envoyée.');

        return $this->redirectToRoute('app_conversations_discussion', ['id' => $conversation->getId()]);
    }

    #[Route('/taskproposal/{id}/accept', name: 'app_task_accept', methods: ['POST'])]
    public function acceptOffer(TaskProposal $taskProposal, StripeService $stripeService, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$taskProposal || $taskProposal->getTask()->getOwner() !== $user) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à accepter cette offre.");
        }

        if ($taskProposal->getStatus() !== 'pending') {
            $this->addFlash('error', 'Cette offre ne peut plus être acceptée.');
            return $this->redirectToRoute('app_conversations_discussion');
        }

        $task = $taskProposal->getTask();

        $taskProposal->setStatus('waiting_payment');
        $task->setStatus('waiting_payment');
        $em->flush();

        $baseUrl = $this->getParameter('base_url');
        $successUrl = $baseUrl . $this->generateUrl('app_task_payment_success', ['id' => $taskProposal->getId()]);
        $cancelUrl = $baseUrl . $this->generateUrl('app_task_payment_cancel');

        try {
            $session = $stripeService->createCheckoutSession(
                $taskProposal->getProposedPrice(),
                'eur',
                $successUrl,
                $cancelUrl
            );
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la création du paiement : ' . $e->getMessage());
            return $this->redirectToRoute('app_conversations_discussion');
        }

        return $this->redirect($session->url);
    }

    #[Route('/taskproposal/payment-cancel', name: 'app_task_payment_cancel')]
    public function paymentCancel(): Response
    {
        $this->addFlash('error', 'Paiement annulé.');
        return $this->redirectToRoute('app_conversations_discussion');
    }

    #[Route('/taskproposal/{id}/payment-success', name: 'app_task_payment_success')]
    public function paymentSuccess(TaskProposal $taskProposal, EntityManagerInterface $em, MailerInterface $mailer, StripeService $stripeService, LoggerInterface $logger): Response
    {
        $user = $this->getUser();

        if (!$taskProposal || $taskProposal->getTask()->getOwner() !== $user) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à confirmer ce paiement.");
        }

        if ($taskProposal->getStatus() !== 'waiting_payment') {
            $this->addFlash('error', 'Ce paiement ne peut être validé que lorsque l\'offre est en attente de paiement.');
            return $this->redirectToRoute('app_conversations_discussion');
        }

        $taskProposal->setStatus('accepted');
        $taskProposal->getTask()->setStatus('in_progress');
        $em->flush();
        $logger->info("Statut de la tâche mis à jour avec succès.");

        try {
            $stripe = new \Stripe\StripeClient($stripeService->getSecretKey());
            $logger->info("Connexion à Stripe OK.");

            $customer = $stripe->customers->create([
                'email' => $user->getEmail(),
                'name' => $user->getIdCustomer()->getFirstName() . ' ' . $user->getIdCustomer()->getLastName(),
            ]);
            $logger->info("Client Stripe créé : " . $customer->id);

            $invoiceItem = $stripe->invoiceItems->create([
                'customer' => $customer->id,
                'amount' => $taskProposal->getProposedPrice() * 100, 
                'currency' => 'eur',
                'description' => 'Paiement pour la tâche : ' . $taskProposal->getTask()->getTitle(),
            ]);
            $logger->info("Article ajouté à la facture.");

            $invoice = $stripe->invoices->create([
                'customer' => $customer->id,
                'collection_method' => 'send_invoice',
                'days_until_due' => 7, 
            ]);
            $logger->info("Facture créée : " . $invoice->id);

            $finalizedInvoice = $stripe->invoices->finalizeInvoice($invoice->id);
            $logger->info("Facture finalisée.");

            if (!isset($finalizedInvoice->hosted_invoice_url)) {
                throw new \Exception("L'URL de la facture n'a pas été générée par Stripe.");
            }
            $logger->info("URL de la facture Stripe : " . $finalizedInvoice->hosted_invoice_url);

            $email = (new Email())
                ->from('no-reply@ton-site.com')
                ->to($user->getEmail())
                ->subject('Votre facture est disponible')
                ->text(
                    "Bonjour,\n\nVotre paiement a été reçu pour la tâche \"" . $taskProposal->getTask()->getTitle() . "\".\n\n"
                    . "Accédez à votre facture ici : " . $finalizedInvoice->hosted_invoice_url
                );

            $mailer->send($email);
            $logger->info("Email envoyé avec succès à " . $user->getEmail());

            $this->addFlash('success', 'Paiement réussi, tâche acceptée et facture envoyée.');

        } catch (\Exception $e) {
            $logger->error("Erreur : " . $e->getMessage());
            $this->addFlash('error', 'Erreur lors de la création de la facture : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_conversations_discussion');
    }

    #[Route('/message/{id}/refuse', name: 'app_task_refuse', methods: ['POST'])]
    public function refuseOffer(Message $message, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $taskProposal = $message->getTaskProposal();

        if (!$taskProposal) {
            throw $this->createNotFoundException("Aucune offre trouvée pour ce message.");
        }

        if ($taskProposal->getTask()->getOwner() !== $user) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à refuser cette offre.");
        }

        $conversation = $message->getIdConversation();

        $taskProposal->setStatus('refused');
        $em->flush();

        $this->addFlash('error', 'Offre refusée.');

        return $this->redirectToRoute('app_conversations_discussion', ['id' => $conversation?->getId()]);
    }
}

/* web hooks
use Stripe\Webhook;
use Stripe\StripeClient;

class PaymentController extends AbstractController
{
    #[Route('/taskproposal/{id}/payment-success', name: 'app_task_payment_success')]
    public function paymentSuccess(
        TaskProposal $taskProposal,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        StripeService $stripeService,
        LoggerInterface $logger
    ): Response {
        $user = $this->getUser();

        if (!$taskProposal || $taskProposal->getTask()->getOwner() !== $user) {
            throw $this->createAccessDeniedException(
                "Vous n'êtes pas autorisé à confirmer ce paiement."
            );
        }

        if ($taskProposal->getStatus() !== 'waiting_payment') {
            $this->addFlash(
                'error',
                "Ce paiement ne peut être validé que lorsque l'offre est en attente de paiement."
            );
            return $this->redirectToRoute('app_conversations_discussion');
        }

        try {
            $stripe = new StripeClient($stripeService->getSecretKey());
            $logger->info("Connexion à Stripe OK.");

            $customer = $stripe->customers->create([
                'email' => $user->getEmail(),
                'name'  => $user->getIdCustomer()->getFirstName() . ' ' . $user->getIdCustomer()->getLastName(),
            ]);
            $logger->info("Client Stripe créé : " . $customer->id);

            $invoiceItem = $stripe->invoiceItems->create([
                'customer'    => $customer->id,
                'amount'      => $taskProposal->getProposedPrice() * 100,
                'currency'    => 'eur',
                'description' => 'Paiement pour la tâche : ' . $taskProposal->getTask()->getTitle(),
            ]);
            $logger->info("Article ajouté à la facture.");

            $invoice = $stripe->invoices->create([
                'customer'         => $customer->id,
                'collection_method'=> 'send_invoice',
                'days_until_due'   => 7,
            ]);
            $logger->info("Facture créée : " . $invoice->id);

            $finalizedInvoice = $stripe->invoices->finalizeInvoice($invoice->id);
            $logger->info("Facture finalisée.");

            if (!isset($finalizedInvoice->hosted_invoice_url)) {
                throw new \Exception("L'URL de la facture n'a pas été générée par Stripe.");
            }

            $taskProposal->setInvoiceId($finalizedInvoice->id);
            $em->flush();

            $logger->info("URL de la facture Stripe : " . $finalizedInvoice->hosted_invoice_url);

            $email = (new Email())
                ->from('no-reply@ton-site.com')
                ->to($user->getEmail())
                ->subject('Votre facture est disponible')
                ->text(
                    "Bonjour,\n\n" .
                    "Votre facture est prête pour la tâche : \"" . $taskProposal->getTask()->getTitle() . "\".\n\n" .
                    "Cliquez ici pour la consulter : " . $finalizedInvoice->hosted_invoice_url
                );

            $mailer->send($email);
            $logger->info("Email envoyé avec succès à " . $user->getEmail());

            $this->addFlash(
                'success',
                'Tâche en attente de paiement. Une facture vous a été envoyée par email.'
            );

        } catch (\Exception $e) {
            $logger->error("Erreur Stripe : " . $e->getMessage());
            $this->addFlash('error', 'Erreur lors de la création de la facture : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_conversations_discussion');
    }

    #[Route('/stripe/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function stripeWebhook(
        Request $request,
        LoggerInterface $logger,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): JsonResponse {
        $payload     = $request->getContent();
        $sigHeader   = $request->headers->get('stripe-signature');
        $endpointSecret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? null;

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            $logger->error('Invalid payload');
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            $logger->error('Invalid signature');
            return new JsonResponse(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'invoice.payment_succeeded':
                /** @var \Stripe\Invoice $invoice *//*
                $invoice = $event->data->object;
                $invoiceId = $invoice->id;
                $customerEmail = $invoice->customer_email;

                $taskProposal = $em->getRepository(TaskProposal::class)->findOneBy(['invoiceId' => $invoiceId]);
                if ($taskProposal) {
                    $taskProposal->setStatus('accepted');
                    $taskProposal->getTask()->setStatus('in_progress');
                    $em->flush();

                    $logger->info("Paiement confirmé pour la facture: $invoiceId. Email client: $customerEmail");

                    $user = $taskProposal->getTask()->getOwner();
                    $email = (new Email())
                        ->from('no-reply@ton-site.com')
                        ->to($user->getEmail())
                        ->subject('Paiement confirmé pour votre tâche')
                        ->text(
                            "Bonjour,\n\n\"" .
                            "Votre paiement pour la tâche '\"" . $taskProposal->getTask()->getTitle() . "' est confirmé !\\n\"" .
                            "Vous pouvez retrouver votre facture ici : " . ($invoice->hosted_invoice_url ?? 'Non disponible')
                        );
                    $mailer->send($email);
                    $logger->info("Email de confirmation envoyé à \"" . $user->getEmail());
                } else {
                    $logger->error("Aucune proposition de tâche trouvée pour la facture: $invoiceId\"");
                }
                break;


            default:
                $logger->info("Événement Stripe non géré: \"" . $event->type);
        }

        return new JsonResponse(['status' => 'success']);
    }
}
*/