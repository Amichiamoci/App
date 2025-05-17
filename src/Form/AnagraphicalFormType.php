<?php

namespace App\Form;

use App\Entity\Anagraphical;
use App\Form\Type\IdentityDocumentType;
use App\Form\Type\SubscribeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class AnagraphicalFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'Id', type: HiddenType::class, options: [
                'required' => false,
            ])
            ->add(child: 'Name', type: TextType::class, options: [
                'required' => true,
                'label' => 'Nome',
                'attr' => [
                    'placeholder' => 'Nome',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
                'invalid_message' => 'Per favore, indica un nome',
            ])
            ->add(child: 'Surname', type: TextType::class, options: [
                'required' => true,
                'label' => 'Cognome',
                'attr' => [
                    'placeholder' => 'Cognome',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
                'invalid_message' => 'Per favore, indica un cognome',
            ])
            ->add(child: 'TaxCode', type: TextType::class, options: [
                'required' => true,
                'label' => 'Codice Fiscale',
                'attr' => [
                    'placeholder' => 'Codice Fiscale',
                    'pattern' => Anagraphical::TaxCodePattern,
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
                'constraints' => [
                    new Regex(
                        pattern: '/' . Anagraphical::TaxCodePattern . '/',
                        message: 'Codice Fiscale non valido',
                    ),
                ],
                'invalid_message' => 'Per favore, indica un codice fiscale valido',
            ])
            ->add(child: 'BirthDate', type: HiddenType::class, options: [
                'required' => false,
            ])
            ->add(child: 'BirthPlace', type: HiddenType::class, options: [
                'required' => false,
            ])
            /*
            ->add(child: 'Email', type: EmailType::class, options: [
                'required' => true,
                'label' => 'Email',
                'attr' => [
                    'autocomplete' => 'email',
                    'placeholder' => 'esampio@mail.it',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
            ])
            */
            ->add(child: 'Email', type: HiddenType::class, options: [
                'required' => false, // Will be set in the controller by user account
            ])
            ->add(child: 'Phone', type: TelType::class, options: [
                'required' => false,
                'label' => 'Telefono (opzionale)',
                'attr' => [
                    'placeholder' => '314 159 2653',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
                //'help' => 'Questo campo è opzionale',
            ])

            ->add(child: 'Document', type: IdentityDocumentType::class, options: [
                'label' => false,
                'required' => true,
                'document_types' => $options['document_types'],
                'require_file' => (bool)$options['anagraphical_only']
            ])
        ;

        if (!$options['anagraphical_only'])
        {
            $builder
                ->add(child: 'Subscription', type: SubscribeType::class, options: [
                    'label' => false,
                    'required' => true,
                    'churches' => $options['churches'],
                ]);
        }

        $builder
            ->add(child: 'signup', type: SubmitType::class, options: [
                'label' => 'Invia i dati',
            ]);

        $builder->setMethod(method: 'POST');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault(option: 'document_types', value: []);
        $resolver->setDefault(option: 'churches', value: []);
        $resolver->setDefault(option: 'anagraphical_only', value: false);
        $resolver->setDefaults(defaults: [
            'data_class' => Anagraphical::class,
            'allow_extra_fileds' => true,
            'allow_file_upload' => true,
        ]);
    }
}
