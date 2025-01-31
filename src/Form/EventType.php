<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Games;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Concept...',
                    'class' => 'form-control mb-4',
                ]   
            ])
            ->add('dateDebut', null, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date de début :",
                'attr' => [
                    'class' => 'form-control mb-4',
                ]   
            ])
            ->add('dateFin', null, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date de fin :",
                'attr' => [
                    'class' => 'form-control mb-4',
                ]   
            ])
            ->add('img', FileType::class, [
                'required' => true,
                'data_class' => null, 
                'label' => 'Image (400x521)',
                'attr' => [
                    'class' => 'form-control mb-4',
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
            ->add('inGame', CheckboxType::class, [
                'required' => false,
                'label' => "Évènement en jeu ?  ",  
            ])
            ->add('game', EntityType::class, [
                'class' => Games::class,
                'label' => "Jeu :",
                'choice_label' => 'name',
                'attr' => [
                    'placeholder' => 'Tag...',
                    'class' => 'form-select mb-4',
                ]
            ])        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
