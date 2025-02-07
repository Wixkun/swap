<?php

namespace App\Controller;

use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Agent;
use App\Entity\Customer;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ProfileType::class, $user);

        if ($user->getIdAgent()) {
            $form->get('pseudoAgent')->setData($user->getIdAgent()->getPseudo());
            $form->get('phoneAgent')->setData($user->getIdAgent()->getPhoneNumber());
        }
        if ($user->getIdCustomer()) {
            $form->get('firstNameCustomer')->setData($user->getIdCustomer()->getFirstName());
            $form->get('lastNameCustomer')->setData($user->getIdCustomer()->getLastName());
            $form->get('cityCustomer')->setData($user->getIdCustomer()->getCity());
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            if ($user->getIdAgent()) {
                $user->getIdAgent()->setPseudo($form->get('pseudoAgent')->getData());
                $user->getIdAgent()->setPhoneNumber($form->get('phoneAgent')->getData());
            }

            if ($user->getIdCustomer()) {
                $user->getIdCustomer()->setFirstName($form->get('firstNameCustomer')->getData());
                $user->getIdCustomer()->setLastName($form->get('lastNameCustomer')->getData());
                $user->getIdCustomer()->setCity($form->get('cityCustomer')->getData());
            }

            $em->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour.');

            return $this->redirectToRoute('app_profil');
        }

        $tasks = $user->getTasks();

        return $this->render('profil/index.html.twig', [
            'user'  => $user,
            'form'  => $form->createView(),
            'tasks' => $tasks,
        ]);
    }

    #[Route('/profil/delete', name: 'app_profil_delete', methods: ['POST'])]
    public function deleteAccount(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token de sécurité invalide.');
            return $this->redirectToRoute('app_profil');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_logout');
    }
}
