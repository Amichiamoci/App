<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Repository\ApiManager;
use App\Form\AddRoleToUserFormType;
use App\Repository\UserRepository;

#[IsGranted(User::REFEREE)]
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
        return $this->render('teams/team.html.twig', [
            'team' => $team
        ]);
    }

    #[Route('/referee/remove/{id}', name: 'referee_remove',)]
    #[IsGranted(User::ADMIN)]
    public function removeAdmin(
        EntityManagerInterface $entityManager, 
        UserRepository $userRepository, 
        int $id): Response
    {
        $user = $userRepository->find($id);
        if (!isset($user))
        {
            $this->addFlash('error', "Utente '$id' non trovato");
        } else {
            
            $user->removeRole(User::REFEREE);
            $entityManager->persist($user);
            $entityManager->flush();

            $fullName = $user->getName() . ' ' . $user->getSurname();
            $this->addFlash('success', "'$fullName' non è più un arbitro.");
        }

        return $this->redirectToRoute('new_referee');
    }

    
    #[Route('/referee/new', name: 'new_referee')]
    #[IsGranted(User::ADMIN)]
    public function new(Request $request,UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $email = '';
        $form = $this->createForm(AddRoleToUserFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $email = $form->get('email')->getData();

            $user = $userRepository->findOneBy(['email' => $email]);
            if (!isset($user))
            {
                $this->addFlash('error', "Utente '$email' non trovato");
            } else {
                // Add the role and save
                $user->addRole(User::REFEREE);
                $entityManager->persist($user);
                $entityManager->flush();

                $fullName = $user->getName() . ' ' . $user->getSurname();
                $this->addFlash('success', "'$fullName' è ora un arbitro");
            }
        }

        $referees = $userRepository->findByRole(User::REFEREE);

        return $this->render('referee/new.html.twig', [
            'addRefereeForm' => $form,
            'referees' => $referees,
            'allUsers' => array_filter($userRepository->findAll(), function(User $u) {
                return !$u->isReferee();
            }),
        ]);
    }
}