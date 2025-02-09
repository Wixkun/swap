<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\Customer;
use App\Entity\User;
use App\Form\ProfileType;
use App\Form\AgentSkillType;
use App\Form\BecomeCustomerType;
use App\Form\BecomeAgentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        if (in_array('ROLE_AGENT', $user->getRoles(), true) && !$user->getIdAgent()) {
            $agent = new Agent();
            $agent->setIdUser($user);
            $user->setIdAgent($agent);
            $em->persist($agent);
        }
        if (in_array('ROLE_CUSTOMER', $user->getRoles(), true) && !$user->getIdCustomer()) {
            $customer = new Customer();
            $customer->setIdUser($user);
            $user->setIdCustomer($customer);
            $em->persist($customer);
        }

        $profileForm = $this->createForm(ProfileType::class, $user);
        if ($user->getIdAgent()) {
            $profileForm->get('pseudoAgent')->setData($user->getIdAgent()->getPseudo());
            $profileForm->get('phoneAgent')->setData($user->getIdAgent()->getPhoneNumber());
        }
        if ($user->getIdCustomer()) {
            $profileForm->get('firstNameCustomer')->setData($user->getIdCustomer()->getFirstName());
            $profileForm->get('lastNameCustomer')->setData($user->getIdCustomer()->getLastName());
            $profileForm->get('cityCustomer')->setData($user->getIdCustomer()->getCity());
        }
        $profileForm->handleRequest($request);
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $plainPassword = $profileForm->get('plainPassword')->getData();
            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
            if ($user->getIdAgent()) {
                $user->getIdAgent()->setPseudo($profileForm->get('pseudoAgent')->getData());
                $user->getIdAgent()->setPhoneNumber($profileForm->get('phoneAgent')->getData());
            }
            if ($user->getIdCustomer()) {
                $user->getIdCustomer()->setFirstName($profileForm->get('firstNameCustomer')->getData());
                $user->getIdCustomer()->setLastName($profileForm->get('lastNameCustomer')->getData());
                $user->getIdCustomer()->setCity($profileForm->get('cityCustomer')->getData());
            }
            $em->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour.');
            return $this->redirectToRoute('app_profil');
        }

        $agentSkillFormView = null;
        if (in_array('ROLE_AGENT', $user->getRoles(), true) && $user->getIdAgent()) {
            $agentSkillForm = $this->createForm(AgentSkillType::class, $user->getIdAgent());
            $agentSkillForm->handleRequest($request);
            if ($agentSkillForm->isSubmitted() && $agentSkillForm->isValid()) {
                $em->flush();
                $this->addFlash('success', 'Vos skills ont été mis à jour.');
                return $this->redirectToRoute('app_profil');
            }
            $agentSkillFormView = $agentSkillForm->createView();
        }

        $tasks = $user->getTasks();

        return $this->render('profil/index.html.twig', [
            'user' => $user,
            'profileForm' => $profileForm->createView(),
            'agentSkillForm' => $agentSkillFormView,
            'tasks' => $tasks,
        ]);
    }

    #[Route('/profil/delete', name: 'app_profil_delete', methods: ['POST'])]
    public function deleteAccount(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token de sécurité invalide.');
            return $this->redirectToRoute('app_profil');
        }
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('app_logout');
    }

    #[Route('/devenir-customer', name: 'app_become_customer')]
    public function becomeCustomer(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$user->getIdCustomer()) {
            $customer = new Customer();
            $customer->setIdUser($user);
            $user->setIdCustomer($customer);
            $em->persist($customer);
        } else {
            $customer = $user->getIdCustomer();
        }
        $form = $this->createForm(BecomeCustomerType::class, $customer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $roles = $user->getRoles();
            if (!in_array('ROLE_CUSTOMER', $roles, true)) {
                $roles[] = 'ROLE_CUSTOMER';
                $user->setRoles($roles);
            }
            $em->flush();
            $this->addFlash('success', 'Vous êtes maintenant un customer et pouvez poster des tâches !');
            return $this->redirectToRoute('app_profil');
        }
        return $this->render('profil/become_customer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/devenir-agent', name: 'app_become_agent')]
    public function becomeAgent(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$user->getIdAgent()) {
            $agent = new Agent();
            $agent->setIdUser($user);
            $user->setIdAgent($agent);
            $em->persist($agent);
        } else {
            $agent = $user->getIdAgent();
        }
        $form = $this->createForm(BecomeAgentType::class, $agent);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $roles = $user->getRoles();
            if (!in_array('ROLE_AGENT', $roles, true)) {
                $roles[] = 'ROLE_AGENT';
                $user->setRoles($roles);
            }
            $em->flush();
            $this->addFlash('success', 'Vous êtes maintenant un agent et pouvez répondre à des offres !');
            return $this->redirectToRoute('app_profil');
        }
        return $this->render('profil/become_agent.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
