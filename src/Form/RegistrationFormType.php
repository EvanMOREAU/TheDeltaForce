<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Email...']
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => false, // On ne veut pas de label intégré par Symfony
                'required' => true, // S'assure que le champ est requis
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les termes et accords.',
                    ]),
                ],
                // 'attr' => ['class' => 'container-checkbox'], // Appliquer la classe personnalisée
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,  
                'required' => true,
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'required' => true,
                'first_options'  => [
                    'label' => false,
                    'attr' => ['placeholder' => 'Mot de passe...'],
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => ['placeholder' => 'Répétez le mot de passe...'],
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 12,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // La contrainte de longueur maximale de 4096 est déjà configurée
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
