<?php

namespace App\Form\Type;

use App\Entity\Church\Church;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChurchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'Id', type: ChoiceType::class, options: [
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
        $resolver->setDefaults(defaults: [
            'data_class' => Church::class,
        ]);
        $resolver->setDefault(option: 'churches', value: []);
    }
}
