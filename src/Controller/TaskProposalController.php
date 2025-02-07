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

class TaskProposalController extends AbstractController
{
    #[Route('/offer/make/{id}', name: 'app_make_offer', methods: ['POST'])]
    public function makeOffer(Request $request, Task $task, EntityManagerInterface $em, ConversationRepository $conversationRepository): Response
    {
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
            return $this->redirectToRoute('app_conversations');
        }

        $taskProposal->setStatus('waiting_payment');
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
            return $this->redirectToRoute('app_conversations');
        }

        return $this->redirect($session->url);
    }

    #[Route('/taskproposal/payment-cancel', name: 'app_task_payment_cancel')]
    public function paymentCancel(): Response
    {
        $this->addFlash('error', 'Paiement annulé.');
        return $this->redirectToRoute('app_conversations');
    }

    #[Route('/taskproposal/{id}/payment-success', name: 'app_task_payment_success')]
    public function paymentSuccess(TaskProposal $taskProposal, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$taskProposal || $taskProposal->getTask()->getOwner() !== $user) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à confirmer ce paiement.");
        }

        if ($taskProposal->getStatus() !== 'waiting_payment') {
            $this->addFlash('error', 'Ce paiement ne peut être validé que lorsque l\'offre est en attente de paiement.');
            return $this->redirectToRoute('app_conversations');
        }

        $taskProposal->setStatus('accepted');
        $taskProposal->getTask()->setStatus('in_progress');
        $em->flush();

        $this->addFlash('success', 'Paiement réussi et offre acceptée.');

        return $this->redirectToRoute('app_conversations');
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
