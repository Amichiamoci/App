<?php

namespace App\Controller;

use App\Repository\ApiManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(ApiManager $apiManager): Response
    {
        return $this->render('profile/index.html.twig', [
            'anagraphicals' => $apiManager->ManagedAnagraphicals($this->getUser()->getUserIdentifier())
        ]);
    }

}