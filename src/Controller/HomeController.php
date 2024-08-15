<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    //
    // Dynamic pages
    //

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
    
    #[Route('/matches', name: 'matches',)]
    public function matches(): Response
    {
        return $this->render('home/matches.html.twig');
    }
    
    #[Route('/events', name: 'events',)]
    public function events(): Response
    {
        return $this->render('home/events.html.twig');
    }

    //
    // Static pages
    //

    #[Route('/home/privacy', name: 'home_privacy',)]
    public function privacy(): Response
    {
        return $this->render('home/privacy.html.twig', [
            'dev_email' => $_ENV["DEV_EMAIL"] ?? 'dev@email.com'
        ]);
    }
    #[Route('/home/credits', name: 'home_credits',)]
    public function credits(): Response
    {
        return $this->render('home/credits.html.twig', [
            'dev_email' => $_ENV["DEV_EMAIL"] ?? 'dev@email.com',
            'repo_url' => $_ENV["REPO_URL"] ?? 'https://github.com'
        ]);
    }
}