<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Service\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Controller handling user registration and email verification.
 */
class RegistrationController extends AbstractController
{
    /**
     * @var EmailVerifier Handles email verification logic.
     */
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    /**
     * Handles user registration process.
     * 
     * @param Request $request HTTP request object
     * @param UserPasswordHasherInterface $userPasswordHasher Password hashing service
     * @param EntityManagerInterface $entityManager Doctrine entity manager
     * @param SendEmailService $mail Email sending service
     * 
     * @return Response Returns the registration form or redirects after success
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SendEmailService $mail): Response
    {
        // Create a new user entity
        $user = new User();
        
        // Create and handle the registration form
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // If the form is submitted and valid, process the registration
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Get plain password from the form
            $plainPassword = $form->get('plainPassword')->getData();

            // Hash and set the password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Persist user to the database
            $entityManager->persist($user);
            $entityManager->flush();

            // Generate email verification link
            $verificationLink = $this->generateUrl('verify_user', [
                'email' => $user->getEmail(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            // Send verification email
            $mail->send(
                'no-reply@openblog.test',
                $user->getEmail(),
                'Activate your account',
                'confirmation_email', 
                ['user' => $user, 'verificationLink' => $verificationLink]
            );

            // Redirect to home page after successful registration
            return $this->redirectToRoute('app_home');
        }

        // Render the registration form
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /**
     * Handles email verification process.
     * 
     * @param Request $request HTTP request object
     * @param TranslatorInterface $translator Translator for error messages
     * 
     * @return Response Redirects to home or registration page based on success or failure
     */
    #[Route('/verify/email', name: 'verify_user')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        try {
            // Retrieve the authenticated user
            $user = $this->getUser();
            
            // Handle email confirmation
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            // If verification fails, show error message and redirect to registration page
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_register');
        }

        // Show success message and redirect to home
        $this->addFlash('success', 'Your email address has been verified.');
        return $this->redirectToRoute('app_home');
    }
}
