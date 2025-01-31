<?php

namespace App\Form;

use App\Entity\Grades;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class GradesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Grade...',
                    'class' => 'form-control mb-4',
                ]
            ])
            ->add('levelRank', NumberType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'min' => 0, // valeur minimale
                    'max' => 100, // valeur maximale
                    'step' => 1, // incrémentation
                    'class' => 'form-control',
                    'Placeholder' => 'Niveau du grade (Entier positif 0/1/2/3/...)'
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Grades::class,
        ]);
    }
}
