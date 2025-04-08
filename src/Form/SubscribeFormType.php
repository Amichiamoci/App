<?php

namespace App\Form;

use App\Entity\Subscription;
use App\Entity\Church\Church;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscribeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'Id', type: HiddenType::class, options: [
                'required' => false,
            ])
            ->add(child: 'AnagraphicalId', type: HiddenType::class, options: [
                'required' => true,
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
            ])
            ->add(child: 'ChurchId', type: ChoiceType::class, options: [
                'required' => true,
                'label' => 'Parrocchia',
                'choices' => self::build_churches_list(churches: $options['churches']),
            ])
            ->add(child: 'subscribe', type: SubmitType::class, options: [
                'label' => 'Iscriviti'
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
