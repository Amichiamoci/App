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
        EntityManagerInterface $entityManager, 
        UserRepository $userRepository, 
        int $id,
    ): Response
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
        $email = '';
        $form = $this->createForm(type: AddRoleToUserFormType::class);
        $form->handleRequest(request: $request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            /**
             * @var ?string
             */
            $email = $form->get(name: 'email')->getData();

            /**
             * @var ?User
             */
            $user = $userRepository->findOneBy(criteria: ['email' => $email]);
            if ($user === null)
            {
                $this->addFlash(type: 'error', message: "Utente '$email' non trovato");
            } else {
                // Add the role and save
                $user->addRole(role: User::REFEREE);
                $entityManager->persist(object: $user);
                $entityManager->flush();

                $fullName = $user->getName();
                $this->addFlash(type: 'success', message: "'$fullName' è ora un arbitro");
            }
        }

        $referees = $userRepository->findByRole(role: User::REFEREE);

        return $this->render(view: 'referee/new.html.twig', parameters: [
            'addRefereeForm' => $form,
            'referees' => $referees,
            'allUsers' => array_filter(array: $userRepository->findAll(), callback: function (User $u): bool {
                return !$u->isReferee();
            }),
        ]);
    }

    #[Route('/referee/result/delete/{id}', name: 'delete_result',)]
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

    #[Route('/referee/result/add/{id}', name: 'add_result', methods: 'POST')]
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