<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use App\Entity\User;
use App\Repository\UserRepository;

class UserProvider implements UserProviderInterface, OAuthAwareUserProviderInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) { }

    public function loadUserByUsername(string $username): UserInterface
    {
        $user = $this->userRepository->findOneBy(['email' => $username]);

        if (!isset($user)) {

            // TODO: add a new user account

            throw new AuthenticationException(
                sprintf('Utente "%s" non trovato.', $username));
        }

        return $user;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->loadUserByUsername($identifier);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new AuthenticationException(
                sprintf('Istanze di "%s" non sono supportate.', User::class));
        }

        if ($this->userRepository instanceof UserProviderInterface) {
            $refreshedUser = $this->userRepository->refreshUser($user);
        } else {
            $refreshedUser = $this->userRepository->find($user->getId());
            if (null === $refreshedUser) {
                throw new AuthenticationException(
                    sprintf('Utente con id %s non trovato', json_encode($user->getId())));
            }
        }

        return $refreshedUser;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        return $this->loadUserByUsername($response->getEmail());
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}