<?php

namespace App\Controller;

use App\Entity\TaskProposal;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{

    #[Route('/payment/success', name: 'app_payment_success')]
    public function success(): Response
    {
        $this->addFlash('success', 'Paiement réussi !');
        return $this->redirectToRoute('app_conversations_discussion');
    }

    #[Route('/payment/cancel', name: 'app_payment_cancel')]
    public function cancel(): Response
    {
        $this->addFlash('error', 'Paiement annulé.');
        return $this->redirectToRoute('app_conversations_discussion');
    }
}
