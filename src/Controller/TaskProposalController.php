<?php
namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskProposal;
use App\Entity\Conversation;
use App\Entity\Message;
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
        $em->persist($message);

        $em->flush();

        $this->addFlash('success', 'Votre offre a bien été envoyée.');

        return $this->redirectToRoute('app_conversations_discussion', ['id' => $conversation->getId()]);
    }
}

