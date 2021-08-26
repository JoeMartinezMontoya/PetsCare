<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\PostSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostSearchType extends AbstractType
{
    private PetsCareFormType $pc;

    public function __construct(PetsCareFormType $pc)
    {
        $this->pc = $pc;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', ChoiceType::class, [
                'required' => true,
                'choices' => $this->pc->getChoices(Post::CATEGORY),
                'label' => "Type d'annonce ?",
                'label_attr' => [
                    'class' => 'text-light'
                ],
            ])
            ->add('created_at', DateType::class, [
                'required' => false,
                'label' => 'Date de parution ?',
                'label_attr' => [
                    'class' => 'text-light'
                ],
                'widget' => 'single_text',
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

    public function getBlockPrefix(): string
    {
        return '';
    }
}
