<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new(     propertyName: 'id',         label: 'ID')->hideOnForm(),
            TextField::new(   propertyName: 'name',       label: 'Nome'),
            EmailField::new(  propertyName: 'email',      label: 'Email'),
            BooleanField::new(propertyName: 'isVerified', label: 'Verificato'),
            ChoiceField::new( propertyName: 'roles',      label: 'Ruoli')
                ->setChoices(choiceGenerator: [
                    'Utente' =>  User::USER,
                    'Admin' =>   User::ADMIN,

                    'Utente esterno' => User::EXTERNAL_PROVIDER,
                    'Google' =>         User::CONNECT_GOOGLE,
                    'Facebook' =>       User::CONNECT_FACEBOOK,
                    'Microsoft' =>      User::CONNECT_MICROSOFT,
                    'GitHub' =>         User::CONNECT_GITHUB,
                ])
                ->allowMultipleChoices()
                ->renderExpanded(expanded: false),
        ];
    }
    
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            return;
        }

        $plainTextPassword = User::RandomPassword(length: 48);

        $hashedPassword = $this->passwordHasher->hashPassword(
            user: $entityInstance,
            plainPassword: $plainTextPassword,
        );

        $entityInstance->setPassword(password: $hashedPassword);
        parent::persistEntity(entityManager: $entityManager, entityInstance: $entityInstance);

        // For the future: send password by email here
    }
}
