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


//#[IsGranted(User::ADMIN)]
class AdminController extends AbstractController
{

    #[Route('/admin/remove/{id}', name: 'admin_remove',)]
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
            
            $user->removeRole(User::ADMIN);
            $entityManager->persist($user);
            $entityManager->flush();

            $fullName = $user->getName() . ' ' . $user->getSurname();
            $this->addFlash('success', "'$fullName' non è più amministatore.");
        }

        return $this->redirectToRoute('admin');
    }

    #[Route('/admin', name: 'admin',)]
    public function index(
        Request $request,
        UserRepository $userRepository, 
        EntityManagerInterface $entityManager): Response
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
                $user->addRole(User::ADMIN);
                $entityManager->persist($user);
                $entityManager->flush();

                $fullName = $user->getName() . ' ' . $user->getSurname();
                $this->addFlash('success', "'$fullName' è ora un arbitro");
            }
        }

        return $this->render('admin/index.html.twig', [
            'admins' => $userRepository->findByRole(User::ADMIN),
            'allUsers' => array_filter($userRepository->findAll(), function (User $u) {
                return !$u->isAdmin();
            }),
            'addAdminForm' => $form,
        ]);
    }
}