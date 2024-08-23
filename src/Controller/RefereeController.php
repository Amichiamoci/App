<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\User;
use App\Repository\ApiManager;

//#[IsGranted(User::REFEREE)]
class RefereeController extends AbstractController
{
    #[Route('/referee', name: 'referee_dashboard')]
    public function index(ApiManager $apiManager): Response
    {
        return $this->render('referee/index.html.twig', [
            'teams' => $apiManager->Teams()
        ]);
    }

    #[Route('/referee/team/{id}', name: 'team_view')]
    public function team(int $id, ApiManager $apiManager): Response
    {
        $team = $apiManager->Team($id);
        if (!isset($team))
        {
            throw new NotFoundHttpException("Squadra '$id' non trovata");
        }
        return $this->render('referee/team.html.twig', [
            'team' => $team
        ]);
    }
}