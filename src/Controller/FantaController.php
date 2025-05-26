<?php

namespace App\Controller;

use App\Entity\Fanta\Participation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FantaController extends AbstractController
{
    #[Route(path: '/fanta', name: 'fanta',)]
    public function index(): Response
    {
        /**
         * @var \App\Entity\User
         */
        $user = $this->getUser();
        $current_participation = $user->getParticipations()->findFirst(p: function (Participation $p): bool {
            return $p->getYear() === (int)date(format: 'Y');
        });
        if ($current_participation === null)
        {
            // Is not subscribed to the current edition of the Fanta
            return $this->redirectToRoute(route: 'fanta_join');
        }


        return $this->render(
            view: 'fanta/index.html.twig',
            parameters: [

            ],
        );
    }

    #[Route(path: '/fanta/join', name: 'fanta_join',)]
    public function join(): Response
    {


        return $this->render(
            view: 'fanta/join.html.twig',
            parameters: [

            ],
        );
    }
}