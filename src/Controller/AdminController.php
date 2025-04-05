<?php

namespace App\Controller;

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
    public function removeAdmin(
        EntityManagerInterface $entityManager, 
        UserRepository $userRepository, 
        int $id): Response
    {
        $user = $userRepository->find(id: $id);
        if (!isset($user))
        {
            $this->addFlash(type: 'error', message: "Utente '$id' non trovato");
        } else {
            
            $user->removeRole(User::ADMIN);
            $entityManager->persist(object: $user);
            $entityManager->flush();

            $fullName = $user->getName();
            $this->addFlash(type: 'success', message: "'$fullName' non è più amministatore.");
        }

        return $this->redirectToRoute(route: 'admin');
    }

    #[Route(path: '/admin', name: 'admin',)]
    public function index(
        Request $request,
        UserRepository $userRepository, 
        EntityManagerInterface $entityManager): Response
    {
        $email = '';
        $form = $this->createForm(type: AddRoleToUserFormType::class);
        $form->handleRequest(request: $request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $email = $form->get(name: 'email')->getData();

            $user = $userRepository->findOneBy(criteria: ['email' => $email]);
            if (!isset($user))
            {
                $this->addFlash(type: 'error', message: "Utente '$email' non trovato");
            } else {
                // Add the role and save
                $user->addRole(User::ADMIN);
                $entityManager->persist(object: $user);
                $entityManager->flush();

                $fullName = $user->getName();
                $this->addFlash(type: 'success', message: "'$fullName' è ora un amministratore");
            }
        }

        return $this->render(view: 'admin/index.html.twig', parameters: [
            'admins' => $userRepository->findByRole(role: User::ADMIN),
            'allUsers' => array_filter(array: $userRepository->findAll(), callback: function (User $u): bool {
                return !$u->isAdmin();
            }),
            'addAdminForm' => $form,
        ]);
    }
}