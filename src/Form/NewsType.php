<?php

namespace App\Form;

use App\Entity\News;
use App\Entity\Tags;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Contenu...',
                    'class' => 'form-control mb-4',
                ]   
            ])
            ->add('tag', EntityType::class, [
                'label' => "Tag :",
                'class' => Tags::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select mb-4',
                ]   
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
