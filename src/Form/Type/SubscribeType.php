<?php

namespace App\Form\Type;

use App\Entity\Subscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SubscribeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'Id', type: HiddenType::class, options: [
                'required' => false,
            ])
            ->add(child: 'Shirt', type: ChoiceType::class, options: [
                'required' => true,
                'label' => 'Taglia',
                'choices'  => [
                    'Scegli una taglia' => '',
                    'XS' => 'XS',
                    'S' => 'S',
                    'M' => 'M',
                    'L' => 'L',
                    'XL' => 'XL',
                    'XXL' => 'XXL',
                    '3XL' => '3XL',
                ],
                'attr' => [
                    'placeholder' => 'Maglietta',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
            ])
            ->add(child: 'Church', type: ChurchType::class, options: [
                'required' => true,
                'label' => false,
                'churches' => $options['churches'],
            ])
            ->add(child: 'certificate', type: FileType::class, options: [
                'mapped' => false,
                'required' => false,
                'label' => 'Certificato medico',
                'constraints' => [
                    new File(options: [
                        'maxSize' => '64M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Per favore, invia un file PDF, immagine o Documento Word',
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Carica il file',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(defaults: [
            'data_class' => Subscription::class,
        ]);
        $resolver->setDefault(option: 'churches', value: []);
    }
}
