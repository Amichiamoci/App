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

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('name', TextType::class, [
                'label' => 'Nome',
                'required' => true,
            ])
            ->add('surname', TextType::class, [
                'label' => 'Cognome',
                'required' => true
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Ho letto l\'informativa sulla Privacy.',
                    ]),
                ],
                'label' => 'Accetto l\'informativa sulla privacy'
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Per favore, inserisci una password',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'La password deve avere almeno {{ limit }} caratteri',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new PasswordStrength([
                        'message' => 'Password troppo debole. Prova ad aggiungere cifre e caratteri speciali (es: !$%&?.=-)',
                    ]),
                ],
            ])
    
        ;
        if (isset($_ENV["RECAPTCHA3_KEY"]) && isset($_ENV["RECAPTCHA3_SECRET"]))
        {
            $builder
                ->add('captcha', Recaptcha3Type::class, [
                    'constraints' => new Recaptcha3(),
                    'action_name' => 'App_Register',
                    'locale' => 'it',
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
