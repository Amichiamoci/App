<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'email', type: EmailType::class, options: [
                'label' => 'Email',
                'required' => true,
                'attr' => [
                    'placeholder' => 'email@esempio.it',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
            ])
            ->add(child: 'name', type: TextType::class, options: [
                'label' => 'Nome completo',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Pinco Pallino',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
            ])
            ->add(child: 'plainPassword', type: PasswordType::class, options: [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'required' => true,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => 'La tua password super segreta',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
                'label' => 'Password',
                'constraints' => [
                    new NotBlank(options: [
                        'message' => 'Per favore, inserisci una password',
                    ]),
                    new Length(
                        min: 10, 
                        minMessage: 'La password deve avere almeno {{ limit }} caratteri',
                        max: 4096,
                    ),
                    new PasswordStrength(options: [
                        'message' => 'Password troppo debole. Prova ad aggiungere cifre e caratteri speciali (es: !$%&?.=-)',
                    ]),
                ],
            ])
            ->add(child: 'agreeTerms', type: CheckboxType::class, options: [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new IsTrue(options: [
                        'message' => 'Ho letto l\'informativa sulla Privacy.',
                    ]),
                ],
                'label' => 'Accetto l\'informativa sulla privacy'
            ])
    
        ;
        if (isset($_ENV["RECAPTCHA3_KEY"]) && isset($_ENV["RECAPTCHA3_SECRET"]))
        {
            $builder
                ->add(child: 'captcha', type: Recaptcha3Type::class, options: [
                    'constraints' => new Recaptcha3(),
                    'action_name' => 'App_Register',
                    'locale' => 'it',
                ]);
        }
        $builder
            ->add(child: 'submit', type: SubmitType::class, options: [
                'label' => 'Crea account',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(defaults: [
            'data_class' => User::class,
        ]);
    }
}
