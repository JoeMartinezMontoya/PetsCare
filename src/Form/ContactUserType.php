<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Votre nom',
                'label_attr' => [
                    'class' => 'text-light'
                ],
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'label_attr' => [
                    'class' => 'text-light'
                ]
            ])
            ->add('phone', NumberType::class, [
                'required' => false,
                'label' => 'Votre numéro',
                'label_attr' => [
                    'class' => 'text-light'
                ],
                'attr' => [
                    'placeholder' => 'Pas obligé'
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Rassurez le propriétaire :)',
                'label_attr' => [
                    'class' => 'text-light'
                ],
                'attr' => [
                    'rows' => 5
                ]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
