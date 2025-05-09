<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginFormType extends AbstractType
{
    public function __construct(
        private readonly string $csrfTokenId = 'authenticate',
    ) { }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: '_username', type: EmailType::class, options: [
                'label' => 'Email',
                'attr' => [
                    'autocomplete' => 'email',
                    'autofocus' => true,
                    'placeholder' => 'email@esempio.it',
                ],
                'required' => true,
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
            ])
            ->add(child: '_password', type: PasswordType::class, options: [
                'label' => 'Password',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Password segretissima',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
            ])
            ->add(child: '_remember_me', type: CheckboxType::class, options: [
                'label'    => 'Resta collegato',
                'required' => false,
                'mapped'   => false,
            ])
            ->add(child: '_csrf_token', type: HiddenType::class, options: [
                'mapped' => false,
                'data' => $options['csrf_token'],
            ])
            ->add(child: '_login', type: SubmitType::class, options: [
                'label' => 'Accedi'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(defaults: [
            'method' => 'POST',
        ]);
        $resolver->setRequired(optionNames: 'csrf_token');
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
