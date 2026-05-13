<?php

namespace App\Controller;

use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/app_login', name: 'app_login')]
    public function login(
        AuthenticationUtils $authenticationUtils, 
        CsrfTokenManagerInterface $csrfTokenManager,
    ): Response
    {
        /*
        if ($this->isGranted(attribute: 'IS_AUTHENTICATED_FULLY'))
        {
            return $this->redirectToRoute(route: 'home');
        }
        */

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error !== null)
            $this->addFlash(type: 'error', message: $error->getMessage());

        $csrfToken = $csrfTokenManager->getToken(tokenId: 'authenticate')->getValue();
        $form = $this->createForm(
            type: LoginFormType::class, 
            options: [
                'csrf_token' => $csrfToken,
            ]
        );

        return $this->render(
            view: 'security/login.html.twig', 
            parameters: [ 
                'loginForm' => $form,
            ],
        );
    }

    #[Route(path: '/app_logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException(
            message: 'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }
}
