<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use App\Entity\User;
use App\Repository\UserRepository;

class UserProvider implements UserProviderInterface, OAuthAwareUserProviderInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface $entityManager,
    ) { }

    public function loadUserByUsername(string $username): UserInterface
    {
        $user = $this->userRepository->findOneBy(criteria: ['email' => $username]);

        if (!isset($user)) {
            throw new AuthenticationException(
                message: sprintf('Utente "%s" non trovato.', $username));
        }

        return $user;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->loadUserByUsername(username: $identifier);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new AuthenticationException(
                message: sprintf('Istanze di "%s" non sono supportate.',  User::class));
        }

        if ($this->userRepository instanceof UserProviderInterface) {
            $refreshedUser = $this->userRepository->refreshUser(user: $user);
        } else {
            $refreshedUser = $this->userRepository->find(id: $user->getId());
            if (null === $refreshedUser) {
                throw new AuthenticationException(
                    message: sprintf(
                        'Utente con id %s non trovato', 
                    json_encode(value: $user->getId())));
            }
        }

        return $refreshedUser;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        try {
            return $this->loadUserByUsername(username: $response->getEmail());
        } catch (AuthenticationException) {
            return $this->createUserFromOAuthResponse(response: $response);
        }
    }

    private function createUserFromOAuthResponse(UserResponseInterface $response): UserInterface
    {
        $user = new User();
        $user->setEmail(email: $response->getEmail());
        $user->setName(
            name: $response->getRealName() ?? 
                ($response->getFirstName() . ' ' . $response->getLastName()));

        $user->setVerified(isVerified: true);
        $user->addRole(role: User::EXTERNAL_PROVIDER);

        // Generate a random password that the user won't know
        $user->setPassword(
            password: $this->userPasswordHasher->hashPassword(
                user: $user,
                plainPassword: User::RandomPassword(length: 48)
            )
        );

        // Save the new user in the DB
        $this->entityManager->persist(object: $user);
        $this->entityManager->flush();

        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}