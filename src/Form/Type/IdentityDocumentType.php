<?php
namespace App\Form\Type;

use App\Entity\IdentityDocument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;

class IdentityDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'Type', type: IdentityDocumentTypeType::class, options: [
                'required' => true,
                'label' => false,
                'document_types'  => $options['document_types'],
            ])
            ->add(child: 'Code', type: TextType::class, options: [
                'required' => true,
                'label' => 'Codice Documento',
                'attr' => [
                    'placeholder' => 'Codice documento',
                ],
                'row_attr' => [
                    'class' => 'form-floating mb-2',
                ],
            ])
            ->add(child: 'DocumentFile', type: FileType::class, options: [
                'mapped' => false,
                'required' => false,
                'label' => 'File Documento',
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
        $resolver->setDefaults([
            'data_class' => IdentityDocument::class,
        ]);
        $resolver->setDefault('document_types', []);
    }
}
