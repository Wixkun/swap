<?php
// src/Controller/ReviewController.php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\Agent;
use App\Entity\TaskProposal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    #[Route('/task/validate/{id}', name: 'app_task_validate', methods: ['POST'])]
    public function validateTask(Request $request, EntityManagerInterface $em, string $id): Response
    {
        $taskProposal = $em->getRepository(TaskProposal::class)->find($id);
        if (!$taskProposal) {
            throw $this->createNotFoundException('TaskProposal non trouvé');
        }

        $user = $this->getUser();
        if ($taskProposal->getTask()->getOwner() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à valider cette task.');
        }

        $rating = $request->request->get('rating');
        $comment = $request->request->get('comment', '');
        if (!$rating) {
            $this->addFlash('error', 'La note est requise.');
            return $this->redirectToRoute('app_default');
        }

        $review = new Review();
        $review->setRating((int)$rating);
        $review->setComment($comment);
        $review->setCreatedAt(new \DateTimeImmutable());
        $review->setIdAgent($taskProposal->getAgent());

        $taskProposal->setStatus('done');

        $em->persist($review);
        $em->flush();

        $agent = $taskProposal->getAgent(); 
        $this->updateAgentRating($agent, $em); 
        
        $this->addFlash('success', 'La task a été validée et l\'agent noté.');
        return $this->redirectToRoute('app_default');
    }

    private function updateAgentRating(Agent $agent, EntityManagerInterface $entityManager): void
    {
     $reviews = $entityManager->getRepository(Review::class)->findBy(['idAgent' => $agent]);

     if (count($reviews) > 0) {
         $totalRating = array_reduce($reviews, fn($sum, $review) => $sum + $review->getRating(), 0);
         $averageRating = $totalRating / count($reviews);
         $agent->setRatingGlobal(round($averageRating, 1));
     } else {
         $agent->setRatingGlobal(null);
     }

     $entityManager->persist($agent);
     $entityManager->flush();
    }
}
