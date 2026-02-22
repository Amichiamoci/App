<?php

namespace App\Security;

use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if (!is_string(value: $user->getEmail())) {
            throw new AccessDeniedException(message: 'Email non riconosciuta');
        }
    }

    public function checkPostAuth(
        UserInterface $user, 
        ?TokenInterface $token = null
    ): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if (!$user->isVerified()) {
            $email = $user->getEmail();
            throw new CustomUserMessageAccountStatusException(
                message: "Account non verificato: controlla le email inviate a $email e clicca sul link per attivare l'account!");
        }
    }
}