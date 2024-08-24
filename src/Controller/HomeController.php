<?php

namespace App\Controller;

use App\Repository\ApiManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    public function matches(ApiManager $apiManager): Response
    {
        return $this->render('home/matches.html.twig', [
            'matches' => $apiManager->Matches()
        ]);
    }
    
    #[Route('/events', name: 'events',)]
    public function events(ApiManager $apiManager): Response
    {
        return $this->render('home/events.html.twig', [
            'events' => $apiManager->Events()
        ]);
    }

    #[Route('/home/church/{id}', name: 'church_view',)]
    public function church(ApiManager $apiManager, int $id): Response
    {
        $church = $apiManager->Church($id);
        if (!isset($church)) {
            throw new NotFoundHttpException("Parrocchia '$id' non trovata");
        }
        return $this->render('home/church.html.twig', [
            'church' => $church,
        ]);
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