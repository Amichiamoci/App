<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(type: RegistrationFormType::class, data: $user);
        $form->handleRequest(request: $request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                password: $userPasswordHasher->hashPassword(
                    user: $user,
                    plainPassword: $form->get(name: 'plainPassword')->getData()
                )
            );

            $entityManager->persist(object: $user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                verifyEmailRouteName: 'app_verify_email', 
                user: $user,
                email: (new TemplatedEmail())
                    ->from(addresses: new Address(address: $_ENV["NO_REPLY_EMAIL"] ?? "no-reply@localhost", name: 'App Amichiamoci'))
                    ->to(addresses: $user->getEmail())
                    ->subject(subject: 'Conferma la tua email')
                    ->htmlTemplate(template: 'registration/confirmation_email.html.twig')
            );
            
            $this->addFlash(
                type: 'success', 
                message: "Conferma la tua email cliccando sul link che hai ricevuto. Se non vedi nulla, controlla lo SPAM");
            
            return $this->redirectToRoute(route: 'home');
        }

        return $this->render(view: 'registration/register.html.twig', parameters: [
            'registrationForm' => $form,
        ]);
    }

    #[Route(path: '/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get(key: 'id');

        if (null === $id) {
            return $this->redirectToRoute(route: 'app_register');
        }

        $user = $userRepository->find(id: $id);

        if (null === $user) {
            return $this->redirectToRoute(route: 'app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation(request: $request, user: $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash(
                type: 'verify_email_error', 
                message: $translator->trans(
                    id: $exception->getReason(), 
                    parameters: [], 
                    domain: 'VerifyEmailBundle',
                )
            );

            return $this->redirectToRoute(route: 'app_register');
        }

        $this->addFlash(type: 'success', message: 'Il tuo indirizzo email è stato verificato.');

        return $this->redirectToRoute(route: 'home');
    }
}
