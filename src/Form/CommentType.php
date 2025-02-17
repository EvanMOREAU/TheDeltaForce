<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Articles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', null, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Message...',
                    'class' => 'form-control col-md-12',
                ]   
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
