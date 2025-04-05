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
            ->add(child: 'email')
            ->add(child: 'name', type: TextType::class, options: [
                'label' => 'Nome completo',
                'required' => true,
            ])
            ->add(child: 'agreeTerms', type: CheckboxType::class, options: [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(options: [
                        'message' => 'Ho letto l\'informativa sulla Privacy.',
                    ]),
                ],
                'label' => 'Accetto l\'informativa sulla privacy'
            ])
            ->add(child: 'plainPassword', type: PasswordType::class, options: [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(options: [
                        'message' => 'Per favore, inserisci una password',
                    ]),
                    new Length(exactly: [
                        'min' => 10,
                        'minMessage' => 'La password deve avere almeno {{ limit }} caratteri',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new PasswordStrength(options: [
                        'message' => 'Password troppo debole. Prova ad aggiungere cifre e caratteri speciali (es: !$%&?.=-)',
                    ]),
                ],
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
