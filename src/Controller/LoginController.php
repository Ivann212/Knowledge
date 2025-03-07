<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * Handles the user login process.
     * 
     * @param AuthenticationUtils $authenticationUtils Provides authentication utilities, such as retrieving login errors and last entered username.
     * 
     * @return Response Renders the login page with potential error messages.
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Retrieve the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Retrieve the last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // Render the login template with error message and last entered username
        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Handles the user logout process.
     * 
     * This method will never be executed as Symfony handles logout automatically via security.yaml.
     * 
     * @throws \LogicException Always thrown because this method should never be reached.
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
