<?php
namespace App\Security;

use App\Entity\User;
use HWI\Bundle\OAuthBundle\Form\RegistrationFormHandlerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class FormHandler implements RegistrationFormHandlerInterface
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function process(Request $request, FormInterface $form, UserResponseInterface $userInformation): bool
    {
        $user = new User();
        $user->setEmail(email: $userInformation->getEmail());
        $user->setName(
            name: $userInformation->getRealName() ?? 
                ($userInformation->getFirstName() . ' ' . $userInformation->getLastName()));

        $user->setVerified(isVerified: true);
        $user->addRole(role: User::EXTERNAL_PROVIDER);

        $form->setData(modelData: $user);
        $form->handleRequest(request: $request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user->setPassword(
                password: $this->userPasswordHasher->hashPassword(
                    user: $user,
                    plainPassword: User::RandomPassword(length: 48),
                )
            );

            return true;
        }

        return false;
    }
}