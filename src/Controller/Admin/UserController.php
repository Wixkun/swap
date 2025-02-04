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
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user')]
final class UserController extends AbstractController
{
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $roles = $form->get('roles')->getData();
    
            $pseudoAgent = $form->get('pseudoAgent')->getData();
            $phoneAgent = $form->get('phoneAgent')->getData(); 
            $firstNameCustomer = $form->get('firstNameCustomer')->getData();
            $lastNameCustomer = $form->get('lastNameCustomer')->getData();
            $cityCustomer = $form->get('cityCustomer')->getData();
    
            if (in_array('ROLE_AGENT', $roles) && !empty($pseudoAgent) && !empty($phoneAgent)) {
                $agent = new Agent();
                $agent->setPseudo($pseudoAgent);
                $agent->setPhoneNumber($phoneAgent);
                $entityManager->persist($agent);
                $user->setIdAgent($agent);
            }
    
            if (in_array('ROLE_CUSTOMER', $roles) && !empty($firstNameCustomer) 
                && !empty($lastNameCustomer) && !empty($cityCustomer)) {
                $customer = new Customer();
                $customer->setFirstName($firstNameCustomer);
                $customer->setLastName($lastNameCustomer);
                $customer->setcity($cityCustomer);
                $entityManager->persist($customer);
                $user->setIdCustomer($customer);
            }
    
            $entityManager->persist($user);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'roles' => $user->getRoles()
        ]);
    }
    

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserType::class, $user);
    
        if ($user->getIdAgent() !== null) {
            $form->get('pseudoAgent')->setData($user->getIdAgent()->getPseudo());
            $form->get('phoneAgent')->setData($user->getIdAgent()->getPhoneNumber());
        }
    
        if ($user->getIdCustomer() !== null) {
            $form->get('firstNameCustomer')->setData($user->getIdCustomer()->getFirstName());
            $form->get('lastNameCustomer')->setData($user->getIdCustomer()->getLastName());
            $form->get('cityCustomer')->setData($user->getIdCustomer()->getCity());
        }
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $pseudoAgent = $form->get('pseudoAgent')->getData();
            $phoneAgent = $form->get('phoneAgent')->getData();
            $firstNameCustomer = $form->get('firstNameCustomer')->getData();
            $lastNameCustomer = $form->get('lastNameCustomer')->getData();
            $cityCustomer = $form->get('cityCustomer')->getData();
    
            $roles = $form->get('roles')->getData();
            $user->setRoles($roles);
    
            if (in_array('ROLE_AGENT', $roles)) {
                if ($user->getIdAgent() === null) {
                    $agent = new Agent();
                    $user->setIdAgent($agent);
                    $em->persist($agent);
                }
                $user->getIdAgent()->setPseudo($pseudoAgent);
                $user->getIdAgent()->setPhoneNumber($phoneAgent);
            } else {
                $user->setIdAgent(null);
            }
    
            if (in_array('ROLE_CUSTOMER', $roles)) {
                if ($user->getIdCustomer() === null) {
                    $customer = new Customer();
                    $user->setIdCustomer($customer);
                    $em->persist($customer);
                }
                $user->getIdCustomer()->setFirstName($firstNameCustomer);
                $user->getIdCustomer()->setLastName($lastNameCustomer);
                $user->getIdCustomer()->setcity($cityCustomer);
            } else {
                $user->setIdCustomer(null);
            }
    
            $em->flush();
            return $this->redirectToRoute('app_user_index');
        }
    
        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'roles' => $user->getRoles() 
        ]);
    }    

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
