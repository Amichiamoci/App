<?php
namespace App\Form\Type;

use App\Entity\IdentityDocumentType as DocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class IdentityDocumentTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'Id', type: ChoiceType::class, options: [
                'required' => true,
                'label' => 'Tipo Documento',
                'choices'  => self::build_types_list(document_types: $options['document_types']),
                'attr' => [
                    'placeholder' => 'Tipologia del documento',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
                'invalid_message' => 'Per favore, seleziona una tipologia di documento',
            ])
        ;
    }

    private static function build_types_list(array $document_types): array
    {
        $arr = [
            'Scegli' => '',
            'La tessera sanitaria non va bene' => [],
        ];
        foreach ($document_types as $type)
        {
            $arr['La tessera sanitaria non va bene'][$type->Label] = $type->Id;
        }
        return $arr;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(defaults: [
            'data_class' => DocumentType::class,
        ]);
        $resolver->setDefault(option: 'document_types', value: []);
    }
}
