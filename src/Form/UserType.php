<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'required' => false,
                'label' => 'Prénom'
            ])
            ->add('lastname', TextType::class, [
                'required' => false,
                'label' => 'Nom de famille'
            ])
            ->add('username', TextType::class, [
                'required' => false,
                'label' => 'Nom d\'utilisateur'
            ])
            ->add('birthdate', BirthdayType::class, [
                'required' => false,
                'label' => 'Date de naissance',
                'widget' => 'single_text'
            ])
            ->add('phone', TelType::class, [
                'required' => false,
                'label' => 'Numéro de téléphone'
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'label' => 'Adresse mail',
                'disabled' => true
            ])
            ->add('isPetsitter', CheckboxType::class, [
                'required' => false,
                'label' => 'Voulez vous faire du PetSitting ?',
                'label_attr' => [
                    'class' => 'switch-custom'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
