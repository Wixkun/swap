<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConversationController extends AbstractController
{
    #[Route('/conversations/discussion', name: 'app_conversations_discussion', methods: ['GET'])]
    public function discussion(
        Request $request,
        ConversationRepository $conversationRepository,
        MessageRepository $messageRepository
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        if ($user->getIdCustomer() && $user->getIdAgent()) {
            $customerConversations = $conversationRepository->findBy([
                'idCustomer' => $user->getIdCustomer()
            ]);
            $agentConversations = $conversationRepository->findBy([
                'idAgent' => $user->getIdAgent()
            ]);
            $conversations = array_unique(array_merge($customerConversations, $agentConversations));
        } elseif ($user->getIdCustomer()) {
            $conversations = $conversationRepository->findBy([
                'idCustomer' => $user->getIdCustomer()
            ]);
        } elseif ($user->getIdAgent()) {
            $conversations = $conversationRepository->findBy([
                'idAgent' => $user->getIdAgent()
            ]);
        } else {
            $conversations = [];
        }

        $selectedConvId = $request->query->get('conv');

        $selectedConversation = null;
        $messages = [];
        if ($selectedConvId) {
            $selectedConversation = $conversationRepository->find($selectedConvId);
            if ($selectedConversation) {
                $isAgent = $user->getIdAgent() && ($selectedConversation->getIdAgent() === $user->getIdAgent());
                $isCustomer = $user->getIdCustomer() && ($selectedConversation->getIdCustomer() === $user->getIdCustomer());
                if ($isAgent || $isCustomer) {
                    $messages = $messageRepository->findBy(
                        ['idConversation' => $selectedConversation],
                        ['sentAt' => 'ASC']
                    );
                } else {
                    $selectedConversation = null;
                }
            }
        }

        return $this->render('conversation/index.html.twig', [
            'conversations'       => $conversations,
            'selectedConversation'=> $selectedConversation,
            'messages'            => $messages,
        ]);
    }

    #[Route('/conversation/{id}/message', name: 'app_conversation_message_send', methods: ['POST'])]
    public function sendMessage(
        Request $request,
        Conversation $conversation,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $isAgent = $user->getIdAgent() && ($conversation->getIdAgent() === $user->getIdAgent());
        $isCustomer = $user->getIdCustomer() && ($conversation->getIdCustomer() === $user->getIdCustomer());
        if (!$isAgent && !$isCustomer) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à envoyer un message dans cette conversation.');
        }

        $content = trim($request->request->get('content'));
        if (empty($content)) {
            $this->addFlash('error', 'Le message ne peut pas être vide.');
            return $this->redirectToRoute('app_conversations_discussion', ['conv' => $conversation->getId()]);
        }

        $message = new \App\Entity\Message();
        $message->setContent($content);
        $message->setSentAt(new \DateTimeImmutable());
        $message->setIdConversation($conversation);
        $message->setIdUser($user);

        $em->persist($message);
        $em->flush();

        $this->addFlash('success', 'Message envoyé.');
        return $this->redirectToRoute('app_conversations_discussion', ['conv' => $conversation->getId()]);
    }
}
