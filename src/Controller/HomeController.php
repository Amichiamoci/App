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

    #[Route(path: '/', name: 'home',)]
    public function index(ApiManager $apiManager): Response
    {
        $todayMatches = [];
        $user = $this->getUser();
        $show_sub_reminder = false;
        $subscription_problems = 0;
        if ($user !== null)
        {
            $todayMatches = $apiManager->TodayMatchesOfUser(email: $user->getUserIdentifier());
            $show_sub_reminder = !$apiManager->IsSubscribedOrParentOfSubscribed(email: $user->getUserIdentifier());
            $subscription_problems = $apiManager->SubscriptionsWithProblems(email: $user->getUserIdentifier());
        }
        return $this->render(view: 'home/index.html.twig', parameters: [
            'todayMatches' => $todayMatches,
            'leaderboard' => $apiManager->Leaderboard(),
            'showSubscriptionReminder' => $show_sub_reminder,
            'problematicSubscriptionsCount' => $subscription_problems,
        ]);
    }


    #[Route(path: '/leaderboard', name: 'leaderboard',)]
    public function leaderboard(ApiManager $apiManager): Response
    {
        return $this->render(view: 'home/leaderboard/index.html.twig', parameters: [
            'leaderboard' => $apiManager->Leaderboard(),
        ]);
    }
    
    #[Route(path: '/matches/{sport}', name: 'matches',)]
    public function matches(ApiManager $apiManager, string $sport): Response
    {
        return $this->render(view: 'home/matches.html.twig', parameters: [
            'matches' => $apiManager->Matches(sport: $sport),
            'requestedSport' => $sport
        ]);
    }

    #[Route(path: '/home/church/{id}', name: 'church_view',)]
    public function church(ApiManager $apiManager, int $id): Response
    {
        $church = $apiManager->Church(id: $id);
        if (!isset($church)) {
            throw new NotFoundHttpException(message: "Parrocchia '$id' non trovata");
        }
        return $this->render(view: 'home/church.html.twig', parameters: [
            'church' => $church,
        ]);
    }

    #[Route(path: '/home/team/{id}', name: 'home_team_view',)]
    public function team(ApiManager $apiManager, int $id): Response
    {
        $team = $apiManager->Team(id: $id);
        if (!isset($team)) {
            throw new NotFoundHttpException(message: "Squadra '$id' non trovata");
        }
        return $this->render(view: 'teams/team.html.twig', parameters: [
            'team' => $team,
        ]);
    }

    #[Route(path: '/home/tournament/{id}', name: 'home_tournament_view',)]
    public function tournament(ApiManager $apiManager, int $id): Response
    {
        $tournament = $apiManager->Tournament(id: $id);
        if (!isset($tournament)) {
            throw new NotFoundHttpException(message: "Torneo '$id' non trovato");
        }
        return $this->render(view: 'home/tournament.html.twig', parameters: [
            'tournament' => $tournament,
        ]);
    }

    #[Route(path: '/home/tournaments/{sport}', name: 'home_tournament_list',)]
    public function tournament_list(ApiManager $apiManager, string $sport): Response
    {
        return $this->render(view: 'home/tournament_list.html.twig', parameters: [
            'sport' => $sport,
            'tournaments' => $apiManager->TournamentFromSport(sport: $sport),
        ]);
    }

    //
    // Static pages
    //

    #[Route(path: '/home/privacy', name: 'home_privacy',)]
    public function privacy(): Response
    {
        return $this->render(view: 'home/privacy.html.twig');
    }
    #[Route(path: '/home/credits', name: 'home_credits',)]
    public function credits(): Response
    {
        return $this->render(view: 'home/credits.html.twig', parameters: [
            'dev_email' => $_ENV["DEV_EMAIL"] ?? 'dev@email.com',
        ]);
    }
}