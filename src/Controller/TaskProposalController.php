<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskProposalController extends AbstractController
{
    #[Route('/task/proposal', name: 'app_task_proposal')]
    public function index(): Response
    {
        return $this->render('task_proposal/index.html.twig', [
            'controller_name' => 'TaskProposalController',
        ]);
    }
}
