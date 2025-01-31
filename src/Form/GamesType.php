<?php

namespace App\Form;

use App\Entity\Tags;
use App\Entity\Games;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class GamesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom...',
                    'class' => 'form-control mb-4',
                ]
            ])
            ->add('description', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Description...',
                    'class' => 'form-control mb-4',
                ]
            ])
            ->add('img', FileType::class, [
                'required' => true,
                'data_class' => null, 
                'mapped' => false,
                'label' => 'Image (500x723)',
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
            ->add('tag', EntityType::class, [
                'class' => Tags::class,
                'label' => 'Tags',
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => [
                    'placeholder' => 'Tag...',
                    'class' => 'form-control mb-4',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Games::class,
        ]);
    }
}
