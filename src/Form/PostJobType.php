<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostJobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', TextType::class, [
                'label' => 'Où?',
            ])
            ->add('petSittingStart', DateType::class, [
                'label' => 'A partir de quand ?'
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Combien',
                'attr' => [
                    'min' => 1,
                    'max' => 7
                ]
            ])
            ->add('durationType', ChoiceType::class, [
                'label' => 'de temps ?',
                'choices' => $this->getChoices(Post::DURATION)
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Une petite description ?'
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
        }
        return $output;
    }
}
