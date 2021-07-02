<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\PostSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', ChoiceType::class, [
                'required' => false,
                'choices' => $this->getChoices(Post::CATEGORY),
                'label' => false,
                'placeholder' => "Quel type d'annonce ?"
            ])
            ->add('created_at', DateType::class, [
                'required' => false,
                'label' => false,
                'widget' => 'single_text',
                'placeholder' => 'A partir de quand ?'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PostSearch::class,
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }

    public function getChoices($const): array
    {
        $choices = $const;
        $output = [];
        foreach ($choices as $k => $v) {
            $output[$v] = $k;
        }
        return $output;
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
