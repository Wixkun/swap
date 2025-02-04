<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Agent;
use App\Entity\Customer;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $roles = $form->get('roles')->getData();
            $user->setRoles($roles);

            if (in_array('ROLE_AGENT', $roles)) {
                $pseudoAgent = $form->get('pseudoAgent')->getData();
                $phoneAgent  = $form->get('phoneAgent')->getData();

                if (!empty($pseudoAgent) && !empty($phoneAgent)) {
                    $agent = new Agent();
                    $agent->setPseudo($pseudoAgent);
                    $agent->setPhoneNumber($phoneAgent);
                    $entityManager->persist($agent);

                    $user->setIdAgent($agent);
                }
            }

            if (in_array('ROLE_CUSTOMER', $roles)) {
                $firstName = $form->get('firstNameCustomer')->getData();
                $lastName  = $form->get('lastNameCustomer')->getData();
                $city      = $form->get('cityCustomer')->getData();

                if (!empty($firstName) && !empty($lastName) && !empty($city)) {
                    $customer = new Customer();
                    $customer->setFirstName($firstName);
                    $customer->setLastName($lastName);
                    $customer->setCity($city);
                    $entityManager->persist($customer);

                    $user->setIdCustomer($customer);
                }
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            if ($user->getIdAgent()) {
                $form->get('pseudoAgent')->setData($user->getIdAgent()->getPseudo());
                $form->get('phoneAgent')->setData($user->getIdAgent()->getPhoneNumber());
            }
            if ($user->getIdCustomer()) {
                $form->get('firstNameCustomer')->setData($user->getIdCustomer()->getFirstName());
                $form->get('lastNameCustomer')->setData($user->getIdCustomer()->getLastName());
                $form->get('cityCustomer')->setData($user->getIdCustomer()->getCity());
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $roles = $form->get('roles')->getData();
            $user->setRoles($roles);

            if (in_array('ROLE_AGENT', $roles)) {
                $pseudoAgent = $form->get('pseudoAgent')->getData();
                $phoneAgent  = $form->get('phoneAgent')->getData();

                if (!$user->getIdAgent()) {
                    $agent = new Agent();
                    $entityManager->persist($agent);
                    $user->setIdAgent($agent);
                }
                $user->getIdAgent()->setPseudo($pseudoAgent);
                $user->getIdAgent()->setPhoneNumber($phoneAgent);

            } else {
                if ($user->getIdAgent()) {
                    $entityManager->remove($user->getIdAgent());
                    $user->removeAgent(); 
                }
            }

            if (in_array('ROLE_CUSTOMER', $roles)) {
                $firstName = $form->get('firstNameCustomer')->getData();
                $lastName  = $form->get('lastNameCustomer')->getData();
                $city      = $form->get('cityCustomer')->getData();

                if (!$user->getIdCustomer()) {
                    $customer = new Customer();
                    $entityManager->persist($customer);
                    $user->setIdCustomer($customer);
                }
                $user->getIdCustomer()->setFirstName($firstName);
                $user->getIdCustomer()->setLastName($lastName);
                $user->getIdCustomer()->setCity($city);

            } else {
                if ($user->getIdCustomer()) {
                    $entityManager->remove($user->getIdCustomer());
                    $user->removeCustomer();
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index');
    }   

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }
}
