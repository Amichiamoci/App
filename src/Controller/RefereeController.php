<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;

class RefereeController extends AbstractController
{
    #[Route('/referee', name: 'referee_dashboard',)]
    #[IsGranted(User::REFEREE)]
    public function index(): Response
    {
        return $this->render('referee/index.html.twig');
    }

}