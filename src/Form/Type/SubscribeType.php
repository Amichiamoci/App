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
                'invalid_message' => 'Per favore, scegli una taglia tra le seguenti',
            ])
            ->add(child: 'Church', type: ChurchType::class, options: [
                'required' => true,
                'label' => false,
                'churches' => $options['churches'],
            ])
            ->add(child: 'Certificate', type: FileType::class, options: [
                'required' => false,
                'label' => 'Certificato medico',
                'constraints' => [
                    new File(options: [
                        'maxSize' => '64M',
                        'mimeTypes' => [
                            // PDF types
                            'application/pdf',
                            'application/x-pdf',
                            
                            // Word and PowerPoint types
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',

                            // Image types
                            'image/jpeg',
                            'image/png',
                            'image/avif',
                            'image/tiff',
                            'image/webp',
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
                'post_max_size_message' => 'File troppo grande!',
                'invalid_message' => 'Per favore, seleziona un file tra i tipi consentiti',
                'help' => 'Carica un unico PDF, un\'immagine o un documento Word. Il certificato medico può anche essere caricato in futuro. Per giocare, è richiesto un certificato medico sportivo almeno di livello non agonistico',
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
