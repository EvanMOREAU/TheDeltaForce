<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'email-address',
                'required' => true,
                'autofocus' => true
            ]
        ])
        ->add('discordName', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'discord-name',
                'required' => true,
                'autofocus' => true
            ]
        ])
        ->add('img', FileType::class, [
            'required' => false,
            'data_class' => null, 
            'mapped' => false,
            'label' => 'Image',
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
        ->add('userName', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'user-name',
                'required' => true,
                'autofocus' => true
            ]
        ])
        ->add('password', PasswordType::class, [
            'mapped' => false,
            'required' => true,
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'password',
                'required' => true,
                'autofocus' => true
            ]
        ])
        ->add('plainPassword', RepeatedType::class, [
            'mapped' => false,
            'required' => false,
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe doivent correspondre.',
            'first_options'  => [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'new-password',
                ]
            ],
            'second_options' => [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'repeat-password',
                ]
            ],
            'constraints' => [
                new Length([
                    'min' => 12,
                    'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                ]),
                new Regex([
                    'pattern' => '/[0-9]/',
                    'message' => 'Votre mot de passe doit contenir au moins 1 chiffre.',
                ]),
                new Regex([
                    'pattern' => '/[A-Z]/',
                    'message' => 'Votre mot de passe doit contenir au moins 1 lettre majuscule.',
                ]),
                new Regex([
                    'pattern' => '/[a-z]/',
                    'message' => 'Votre mot de passe doit contenir au moins 1 lettre minuscule.',
                ]),
                new Regex([
                    'pattern' => '/[\W_]/',
                    'message' => 'Votre mot de passe doit contenir au moins 1 caractère spécial.',
                ]),
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
