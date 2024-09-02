<?php

namespace App\Controller;

use App\Repository\ApiManager;
use App\Repository\TodaySaintManager;
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
    public function index(ApiManager $apiManager, TodaySaintManager $todaySaintManager): Response
    {
        $todayMatches = [];
        $user = $this->getUser();
        if (isset($user)) {
            $todayMatches = $apiManager->TodayMatchesOfUser($user->getUserIdentifier());
        }
        return $this->render('home/index.html.twig', [
            'todayMatches' => $todayMatches,
            'todaySaint' => $todaySaintManager->Default(),
            'leaderboard' => $apiManager->Leaderboard(),
        ]);
    }

    #[Route('/leaderboard', name: 'leaderboard',)]
    public function leaderboard(ApiManager $apiManager): Response
    {
        return $this->render('home/leaderboard/index.html.twig', [
            'leaderboard' => $apiManager->Leaderboard(),
        ]);
    }
    
    #[Route('/matches/{sport}', name: 'matches',)]
    public function matches(ApiManager $apiManager, string $sport): Response
    {
        return $this->render('home/matches.html.twig', [
            'matches' => $apiManager->Matches($sport),
            'requestedSport' => $sport
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

    #[Route('/home/team/{id}', name: 'home_team_view',)]
    public function team(ApiManager $apiManager, int $id): Response
    {
        $team = $apiManager->Team($id);
        if (!isset($team)) {
            throw new NotFoundHttpException("Squadra '$id' non trovata");
        }
        return $this->render('teams/team.html.twig', [
            'team' => $team,
        ]);
    }

    #[Route('/home/tourney/{id}', name: 'home_tourney_view',)]
    public function tourney(ApiManager $apiManager, int $id): Response
    {
        $tourney = $apiManager->Tourney($id);
        if (!isset($tourney)) {
            throw new NotFoundHttpException("Torneo '$id' non trovato");
        }
        return $this->render('home/tourney.html.twig', [
            'tourney' => $tourney,
        ]);
    }

    #[Route('/home/tourneys/{sport}', name: 'home_tourney_list',)]
    public function tourney_list(ApiManager $apiManager, string $sport): Response
    {
        return $this->render('home/tourney_list.html.twig', [
            'sport' => $sport,
            'tourneys' => $apiManager->TourneyFromSport($sport),
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