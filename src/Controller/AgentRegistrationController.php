<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Agent;
use App\Form\AgentRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AgentRegistrationController extends AbstractController
{
    #[Route('/register/agent', name: 'app_register_agent')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(AgentRegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_AGENT']);

            $entityManager->persist($user);
            $entityManager->flush();

            $pseudo = $form->get('pseudo')->getData();
            $phoneNumber = $form->get('phoneNumber')->getData();

            $agent = new Agent();
            $agent->setPseudo($pseudo);
            $agent->setPhoneNumber($phoneNumber);
            $agent->setIdUser($user);

            $selectedSkills = $form->get('skills')->getData();
            foreach ($selectedSkills as $skill) {
                $agent->addSkill($skill);
            }

            $entityManager->persist($agent);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte agent a été créé avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register_agent.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
