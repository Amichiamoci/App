<?php

namespace App\Controller;

use App\Entity\AddUserRole;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\AddRoleToUserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted(attribute: User::ADMIN)]
class AdminController extends AbstractController
{

    #[Route(path: '/admin/remove/{id}', name: 'admin_remove',)]
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
                
                $user->removeRole(role: User::ADMIN);
                $entityManager->persist(object: $user);
                $entityManager->flush();

                $fullName = $user->getName();
                $this->addFlash(type: 'success', message: "'$fullName' non è più amministatore.");
            }
        }

        return $this->redirectToRoute(route: 'admin');
    }

    #[Route(path: '/admin', name: 'admin',)]
    public function index(
        Request $request,
        UserRepository $userRepository, 
        EntityManagerInterface $entityManager,
    ): Response
    {
        $form = $this->createForm(
            type: AddRoleToUserFormType::class, 
            data: new AddUserRole(role: User::ADMIN),
            options:[
                'users' => array_filter(
                    array: $userRepository->findAll(), 
                    callback: function (User $u): bool {
                        return !$u->isAdmin();
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
            if ($add_role->Role !== User::ADMIN)
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
                $this->addFlash(type: 'success', message: "'$fullName' è ora un amministratore");
                return $this->redirectToRoute(route: 'admin');
            }

            $this->addFlash(type: 'error', message: "Utente non trovato");
            $status_code = 500;
        }

        return $this->render(
            view: 'admin/index.html.twig', 
            parameters: [
                'admins' => $userRepository->findByRole(role: User::ADMIN),
                'addAdminForm' => $form,
            ],
            response: new Response(content: null, status: $status_code),
        );
    }
}