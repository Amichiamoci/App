<?php

namespace App\Security;

use App\Repository\UserRepository;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Doctrine\ORM\EntityManagerInterface;

final class OAuthConnector implements AccountConnectorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly array $properties
    ) {
    }

    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        if (!isset($this->properties[$response->getResourceOwner()->getName()])) {
            return;
        }

        $dbUser = $this->userRepository->find($response->getUserIdentifier());
        if (!isset($dbUser)) {
            throw new \Exception('Utente non trovato');
        }
        
        $property = new PropertyAccessor();
        $property->setValue(
            $user, 
            $this->properties[$response->getResourceOwner()->getName()], 
            $response->getUserIdentifier());
        //$user = $dbUser;

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}