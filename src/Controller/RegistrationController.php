<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Customer;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );
            $user->setPassword($hashedPassword);

            $user->setRoles(['ROLE_CUSTOMER']);

            $entityManager->persist($user);
            $entityManager->flush(); 

            $firstName = $form->get('firstName')->getData();
            $lastName = $form->get('lastName')->getData();
            $address = $form->get('address')->getData();

            $customer = new Customer();
            $customer->setFirstName($firstName);
            $customer->setLastName($lastName);
            $customer->setAddress($address);
            $customer->setIdUser($user); 

            $entityManager->persist($customer);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été créé avec succès.');
            return $this->redirectToRoute('app_default');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
