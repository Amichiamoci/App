<?php

namespace App\Controller;

use App\Repository\ApiManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route(path: '/profile', name: 'profile')]
    public function index(ApiManager $apiManager): Response
    {
        return $this->render(view: 'profile/index.html.twig', parameters: [
            'anagraphicals' => $apiManager->ManagedAnagraphicals(email: $this->getUser()->getUserIdentifier())
        ]);
    }

}