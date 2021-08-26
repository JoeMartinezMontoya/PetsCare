<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Votre nom',
                'label_attr' => [
                    'class' => 'text-light'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse mail',
                'label_attr' => [
                    'class' => 'text-light'
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Que vouliez vous me dire ?',
                'label_attr' => [
                    'class' => 'text-light'
                ],
                'attr' => [
                    'rows' => 6
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Votre numéro',
                'label_attr' => [
                    'class' => 'text-light'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
