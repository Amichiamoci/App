<?php

namespace App\Form;

use App\Entity\Subscription;
use App\Entity\Church\Church;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add(child: 'ChurchId', type: ChoiceType::class, options: [
                'required' => true,
                'label' => 'Parrocchia',
                'choices' => self::build_churches_list(churches: $options['churches']),
                'attr' => [
                    'placeholder' => 'Parrocchia',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
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
    private static function build_churches_list(array $churches): array
    {
        $arr = [
            'Scegli una Parrocchia' => '',
        ];
        foreach ($churches as $church)
        {
            $arr[$church->Name] = $church->Id;
        }
        return $arr;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
        ]);
        $resolver->setDefault('churches', []);
    }
}
