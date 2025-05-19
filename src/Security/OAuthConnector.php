<?php

namespace App\Security;

use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Doctrine\ORM\EntityManagerInterface;

final class OAuthConnector implements AccountConnectorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly array $properties
    ) {
    }

    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        if (!isset($this->properties[$response->getResourceOwner()->getName()]))
        {
            return;
        }

        $roles = array_filter(array: $user->getRoles(), callback: function (string $role): bool { return $role !== 'ROLE_USER'; });
        $roles[] = 'ROLE_' . strtoupper(string: $this->properties[$response->getResourceOwner()->getName()]);

        $property = new PropertyAccessor();
        $property->setValue(
            objectOrArray: $user, 
            propertyPath: 'roles', 
            value: $roles, 
        );
        //$property->setValue(
        //    objectOrArray: $user, 
        //    propertyPath: 'name', 
        //    value: $response->getRealName() ?? ($response->getFirstName() . ' ' . $response->getLastName()),
        //);
        //$property->setValue(objectOrArray: $user, propertyPath: 'verified', value: true, );
        
        //$property->setValue($user, $this->properties[$response->getResourceOwner()->getName()], $response->getUserIdentifier());

        $this->entityManager->persist(object: $user);
        $this->entityManager->flush();
    }
}