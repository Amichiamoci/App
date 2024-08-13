<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home',)]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/leaderboard', name: 'leaderboard',)]
    public function leaderboard(): Response
    {
        return $this->render('home/leaderboard.html.twig');
    }

    #[Route('/home/privacy', name: 'home_privacy',)]
    public function privacy(): Response
    {
        return $this->render('home/privacy.html.twig', [
            'dev_email' => 'dev@email.com'
        ]);
    }
    #[Route('/home/credits', name: 'home_credits',)]
    public function credits(): Response
    {
        return $this->render('home/credits.html.twig', [
            'dev_email' => 'dev@email.com',
            'repo_url' => 'https://github.com'
        ]);
    }
}