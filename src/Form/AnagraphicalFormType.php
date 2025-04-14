<?php

namespace App\Form;

use App\Entity\Anagraphical;
use App\Form\Type\IdentityDocumentType;
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
            ])
            ->add(child: 'TaxCode', type: TextType::class, options: [
                'required' => true,
                'label' => 'Codice Fiscale',
                'attr' => [
                    'placeholder' => 'Codice Fiscale',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
                'constraints' => [
                    new Regex(
                        pattern: '/[a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9]{3}[a-zA-Z]/',
                        message: 'Codice Fiscale non valido',
                    ),
                ],
            ])
            /*
            ->add(child: 'BirthDate', type: HiddenType::class, options: [
                'required' => true,
            ])
            ->add(child: 'BirthPlace', type: HiddenType::class, options: [
                'required' => true,
            ])
            */
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
            ->add(child: 'Phone', type: TelType::class, options: [
                'required' => false,
                'label' => 'Telefono',
                'attr' => [
                    'placeholder' => '314 159 2653',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
            ])

            ->add(child: 'Document', type: IdentityDocumentType::class, options: [
                'label' => false,
                'required' => true,
                'document_types' => $options['document_types'],
            ])
        ;

        if ($options['anagraphical_only'])
        {
            $builder
                ->add(child: 'signup', type: SubmitType::class, options: [
                    'label' => 'Invia i dati'
                ]);
        } else {
            $builder
                ->add(child: 'Subscription', type: SubscribeType::class, options: [
                    'label' => false,
                    'required' => false,
                    'churches' => $options['churches'],
                ])
                ->add(child: 'subscribe', type: SubmitType::class, options: [
                    'label' => 'Iscriviti'
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('document_types', []);
        $resolver->setDefault('churches', []);
        $resolver->setDefault('anagraphical_only', false);
        $resolver->setDefaults([
            'data_class' => Anagraphical::class,
        ]);
    }
}
