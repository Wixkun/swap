<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AgentController extends AbstractController
{
    #[Route('/agents', name: 'app_agent_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $agents = $entityManager->getRepository(Agent::class)->findBy([], ['ratingGlobal' => 'DESC']);

        return $this->render('review/index.html.twig', [
            'agents' => $agents, 
        ]);
    }
}
