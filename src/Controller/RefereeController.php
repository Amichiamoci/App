<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\User;
use App\Entity\AddUserRole;
use App\Form\AddMatchResultFormType;
use App\Repository\ApiManager;
use App\Repository\UserRepository;
use App\Form\AddRoleToUserFormType;

#[IsGranted(
    attribute: new Expression(
        'is_granted("' . User::REFEREE . '") or is_granted("' . User::ADMIN . '")'
    ),
)]
class RefereeController extends AbstractController
{
    #[Route(path: '/referee', name: 'referee_dashboard')]
    public function index(ApiManager $apiManager): Response
    {
        return $this->render(view: 'referee/index.html.twig', parameters: [
            'sportAndMatches' => $apiManager->TodayAndYesterdayMatches()
        ]);
    }

    #[Route(path: '/referee/teams', name: 'referee_teams')]
    public function teams(ApiManager $apiManager): Response
    {
        return $this->render(view: 'referee/teams.html.twig', parameters: [
            'teams' => $apiManager->Teams()
        ]);
    }

    #[Route(path: '/referee/team/{id}', name: 'team_view')]
    public function team(int $id, ApiManager $apiManager): Response
    {
        $team = $apiManager->Team(id: $id);
        if (!isset($team))
        {
            throw new NotFoundHttpException(message: "Squadra '$id' non trovata");
        }
        return $this->render(view: 'teams/team.html.twig', parameters: [
            'team' => $team
        ]);
    }

    #[Route(path: '/referee/remove/{id}', name: 'referee_remove',)]
    #[IsGranted(attribute: User::ADMIN)]
    public function remove(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager, 
        UserRepository $userRepository, 
    ): Response
    {
        if ($request->isMethod(method: 'POST'))
        {
            /**
             * @var ?User
             */
            $user = $userRepository->find(id: $id);
            if ($user === null)
            {
                $this->addFlash(type: 'error', message: "Utente '$id' non trovato");
            } else {
                
                $user->removeRole(role: User::REFEREE);
                $entityManager->persist(object: $user);
                $entityManager->flush();

                $fullName = $user->getName();
                $this->addFlash(type: 'success', message: "'$fullName' non è più un arbitro.");
            }
        }

        return $this->redirectToRoute(route: 'new_referee');
    }

    
    #[Route(path: '/referee/new', name: 'new_referee')]
    #[IsGranted(attribute: User::ADMIN)]
    public function new (
        Request $request,
        UserRepository $userRepository, 
        EntityManagerInterface $entityManager,
    ): Response
    {
        $form = $this->createForm(
            type: AddRoleToUserFormType::class, 
            data: new AddUserRole(role: User::REFEREE),
            options:[
                'users' => array_filter(
                    array: $userRepository->findAll(), 
                    callback: function (User $u): bool {
                        return !$u->isReferee();
                    },
                )
            ]
        );
        $form->handleRequest(request: $request);
        $status_code = $form->isSubmitted() && !$form->isValid() ? 422 : 200;

        if ($form->isSubmitted() && $form->isValid()) 
        {
            /**
             * @var AddUserRole
             */
            $add_role = $form->getData();
            if ($add_role->Role !== User::REFEREE)
            {
                throw new \InvalidArgumentException(message: 'Trying to set unallowed role via this form');
            }

            /**
             * @var ?User
             */
            $user = $userRepository->findOneBy(criteria: ['email' => $add_role->User]);
            if ($user !== null)
            {
                // All ok
                $user->addRole(role: $add_role->Role);
                $entityManager->persist(object: $user);
                $entityManager->flush();

                $fullName = $user->getName();
                $this->addFlash(type: 'success', message: "'$fullName' è ora un arbitro");
                return $this->redirectToRoute(route: 'new_referee');
            }

            $this->addFlash(type: 'error', message: "Utente non trovato");
            $status_code = 500;
        }

        return $this->render(
            view: 'referee/new.html.twig', 
            parameters: [
                'addRefereeForm' => $form,
                'referees' => $userRepository->findByRole(role: User::REFEREE),
            ],
            response: new Response(content: null, status: $status_code),
        );
    }

    #[Route(path: '/referee/result/delete/{id}', name: 'delete_result',)]
    public function resultDelete(ApiManager $apiManager, int $id): Response
    {
        if ($apiManager->DeleteResult(id: $id)){
            $this->addFlash(type: 'success', 
                message: 'Risultato rimosso');
        } else {
            $this->addFlash(type: 'error', 
                message: 'È avvenuto un errore: non è stato possibile rimuovere il risultato');
        }
        return $this->redirectToRoute(route: 'referee_dashboard');
    }

    #[Route(path: '/referee/result/add/{id}', name: 'add_result', methods: 'POST')]
    public function resultAdd(Request $request, ApiManager $apiManager, int $id): Response
    {
        $content = $request->getPayload()->get(key: 'content');

        $parts = array_values(
            array: array_map(
                callback: function (string $s): string { return trim(string: $s); },
                array: explode(separator: '-', string: $content)
            )
        );
        $score = $apiManager->AddResult(id: $id, home: $parts[0], guest: $parts[1]);
        if (isset($score)) {
            $this->addFlash(type: 'success', 
                message: 'Risultato aggiunto');
        } else {
            $this->addFlash(type: 'error', 
                message: 'È avvenuto un errore: non è stato possibile aggiungere il risultato');
        }
        return $this->redirectToRoute(route: 'referee_dashboard');
    }
}