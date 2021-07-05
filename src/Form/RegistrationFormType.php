<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'required' => true,
                'label' => 'Prénom'
            ])
            ->add('lastname', TextType::class, [
                'required' => true,
                'label' => 'Nom de Famille'
            ])
            ->add('username', TextType::class, [
                'required' => true,
                'label' => 'Nom d\'utilisateur',
                'attr' => [
                    'aria-label' => 'Username',
                    'aria-describedby' => 'basic-addon1'
                ]
            ])
            ->add('birthdate', BirthdayType::class, [
                'required' => true,
                'label' => 'Date de naissance',
                'widget' => 'single_text'
            ])
            ->add('phone', TelType::class, [
                'required' => false,
                'label' => 'Numéro de téléphone'
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Adresse mail'
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'required' => true,
                'attr' => [
                    'autocomplete' => 'new-password'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe devez contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'invalid_message' => 'Les deux champs doivent correspondre',
                'options' => [
                    'attr' =>

                        [
                            'class' => 'password-field'
                        ]
                ],
                'first_options' => [
                    'label' => 'Mot de passe'
                ],
                'second_options' => [
                    'label' => 'Répetez votre mot de passe'
                ]
            ])
            ->add('isPetsitter', CheckboxType::class, [
                'label' => 'Voulez vous faire du PetSitting ?',
                'label_attr' => [
                    'class' => 'switch-custom'
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte les Termes et Conditions',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos Termes et Conditions.',
                    ]),
                ],
                'label_attr' => [
                    'class' => 'switch-custom'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
