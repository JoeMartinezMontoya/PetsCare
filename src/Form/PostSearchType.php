<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\PostSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                'label' => false
            ])
        ;
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
