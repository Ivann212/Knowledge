<?php

namespace App\Security;

use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;


class EmailVerifier
{
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private MailerInterface $mailer;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer, UrlGeneratorInterface $urlGenerator)
    {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
    }

    public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, Email $email): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            $user->getId(),
            $user->getEmail()
        );

        $email->html(sprintf(
            'Veuillez confirmer votre email en cliquant sur ce lien : <a href="%s">Confirmer mon email</a>',
            $signatureComponents->getSignedUrl()
        ));

        $this->mailer->send($email);
    }

    public function handleEmailConfirmation(Request $request, User $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation(
            $request->getUri(),
            $user->getId(),
            $user->getEmail()
        );

        $user->setIsVerified(true); // Ajoute un champ isVerified dans ton entité User
    }
}
