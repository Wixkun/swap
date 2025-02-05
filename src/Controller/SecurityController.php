<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;
use App\Form\RegistrationFormType;

class SecurityController extends AbstractController
{
    #[Route('/access-denied', name: 'app_access_denied')]
    public function accessDenied(): Response
    {
        return $this->render('security/access_denied.html.twig');
    }

    #[Route(path: '/forgot', name: 'page_forgot_password', methods: ['GET'])]
    public function forgotPassword(): Response
    {
        return $this->render('auth/forgot.html.twig');
    }

    #[Route(path: '/forgot/submit', name: 'submit_forgot_password', methods: ['POST'])]
    public function handleForgotPassword(
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $email = $request->request->get('_email');

        if (!$email) {
            $this->addFlash('error', 'Veuillez entrer une adresse email.');
            return $this->redirectToRoute('page_forgot_password');
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            $this->addFlash('error', 'Aucun utilisateur trouvé avec cette adresse email.');
            return $this->redirectToRoute('page_forgot_password');
        }

        $resetToken = Uuid::v4();
        $user->setResetToken($resetToken->toRfc4122());

        $entityManager->persist($user);
        $entityManager->flush();

        $resetLink = $this->generateUrl(
            'page_reset_password',
            ['token' => $user->getResetToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $emailMessage = (new TemplatedEmail())
            ->from('hello@example.com')
            ->to($email)
            ->subject('Réinitialisation de votre mot de passe')
            ->htmlTemplate('auth/reset_email.html.twig')
            ->context([
                'reset_link' => $resetLink,
                'user_email' => $email,
            ]);

        $mailer->send($emailMessage);

        $this->addFlash('success', 'Un email de réinitialisation a été envoyé à votre adresse email.');
        return $this->redirectToRoute('page_forgot_password');
    }

    #[Route(path: '/reset/{token}', name: 'page_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(
        string $token,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Le lien de réinitialisation est invalide ou expiré.');
            return $this->redirectToRoute('page_forgot_password');
        }

        if ($request->isMethod('POST')) {
            $password       = $request->request->get('password');
            $repeatPassword = $request->request->get('repeat_password');

            if ($password !== $repeatPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('page_reset_password', ['token' => $token]);
            }

            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
                $this->addFlash('error', 'Votre mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.');
                return $this->redirectToRoute('page_reset_password', ['token' => $token]);
            }

            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $user->setResetToken(null);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/reset.html.twig', [
            'token' => $token,
        ]);
    }
}
