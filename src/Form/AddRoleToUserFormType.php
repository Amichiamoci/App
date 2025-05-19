<?php

namespace App\Form;

use App\Entity\AddUserRole;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddRoleToUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'User', type: ChoiceType::class, options: [
                'label' => 'Email',
                'required' => true,
                'choices' => self::build_users_list(users: $options['users']),
                'attr' => [
                    'placeholder' => 'email@esempio.it',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
                'invalid_message' => 'Per favore, scegli un utente tra quelli proposti',
            ])
            ->add(child: 'Role', type: HiddenType::class, options: [
                'required' => true,
            ])
            ->add(child: 'submit', type: SubmitType::class, options: [
                'label' => 'Aggiungi ruolo',
            ])
        ;

        $builder->setMethod(method: 'POST');
    }
    private static function build_users_list(array $users): array
    {
        $arr = [
            'Scegli un utente' => '',
        ];
        foreach ($users as $user)
        {
            if (!($user instanceof User))
            {
                continue;
            }
            $arr[$user->getName() . ' (' . $user->getEmail() . ')'] = $user->getEmail();
        }
        return $arr;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault(option: 'users', value: []);
        $resolver->setDefaults(defaults: [
            'data_class' => AddUserRole::class,
        ]);
    }
}
