<?php

namespace App\Form;

use App\Entity\Articles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ArticlesEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre :',
                'required' => false,
                'attr' => [
                    'class' => "form-control mb-3",
                    'placeholder' => "Titre..."
                ]
            ])
            ->add('img', FileType::class, [
                'required' => false,
                'data_class' => null, 
                'mapped' => false,
                'label' => 'Image (400x225)',
                'attr' => [
                    'class' => 'form-control',
                    'type' => 'file',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '20M',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Envoyez une image correcte s\'il vous plaît.',
                    ])
                ],
            ])  
            ->add('text', null, [
                'label' => 'Contenu :',
                'required' => false,
                'attr' => [
                    'class' => "form-control mb-3",
                    'placeholder' => "Contenu de l'article..."
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
