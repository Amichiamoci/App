<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Form\SubscribeFormType;
use App\Repository\ApiManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route(path: '/profile', name: 'profile')]
    public function index(ApiManager $apiManager): Response
    {
        return $this->render(
            view: 'profile/index.html.twig', 
            parameters: [
                'anagraphicals' => $apiManager->ManagedAnagraphicals(email: $this->getUser()->getUserIdentifier())
            ],
        );
    }

    #[Route(path: '/profile/get_involved/{id}', name: 'get_involved')]
    public function get_involved(
        ApiManager $apiManager, 
        Request $request,
        int $id,
    ): Response
    {
        $subscription = new Subscription(anagraphical: $id, shirt: '', church: 0);
        $form = $this->createForm(type: SubscribeFormType::class, data: $subscription);
        $form->handleRequest(request: $request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $subscription = $form->getData();
            // $apiManager->Subscription($subscription);
        }

        return $this->render(
            view: 'profile/subscribe.html.twig', 
            parameters: [
                'form' => $form,
            ],
        );
    }
}