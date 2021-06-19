<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Titre de l'annonce",
                'required' => true,
                'attr' => [
                    'placeholder' => 'Le mieux, ce serait un titre accrocheur...'
                ]
            ])
            ->add('category', ChoiceType::class, [
                'label' => "De quoi ca s'agit ?",
                'required' => true,
                'attr' => [
                    'placeholder' => "Quel genre d'annonce ?"
                ],
                'choices' => $this->getChoices(Post::CATEGORY)
            ])
            ->add('content', TextareaType::class, [
                'label' => "Contenu",
                'required' => true,
                'attr' => [
                    'placeholder' => "Et d'être précis dans vos propos..."
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }

    public function getChoices($const): array
    {
        $choices = $const;
        $output = [];
        foreach ($choices as $k => $v) {
            $output[$v] = $k;

            // "Tout voir" option removal
            if ((int)$output[$v] === -1) {
                unset($output[$v]);
            }
        }
        return $output;
    }
}
