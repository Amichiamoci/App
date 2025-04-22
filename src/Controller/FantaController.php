<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FantaController extends AbstractController
{
    #[Route(path: '/fanta', name: 'fanta',)]
    public function index(): Response
    {
        return $this->render(view: 'fanta/index.html.twig');
    }

}