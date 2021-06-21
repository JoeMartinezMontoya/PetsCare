<?php

namespace App\Form;

use App\Entity\Pet;
use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostFoundType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', TextType::class, [
                'label' => "Où l'avez vous trouvé ?"
            ])
            ->add('species', ChoiceType::class, [
                'label' => "Qu'est ce que c'est ?",
                'mapped' => false,
                'choices' => $this->getChoices(Pet::SPECIES)
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Décrivez le, le plus précis sont les infos, le plus de chances on à de retrouver votre compagnon'
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
