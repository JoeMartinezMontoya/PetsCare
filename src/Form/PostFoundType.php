<?php

namespace App\Form;

use App\Entity\Pet;
use App\Entity\Post;
use App\Entity\Tags;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
                'choices' => $this->getChoices(Pet::SPECIES)
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Décrivez le',
                'attr' => [
                    'rows' => 5
                ]
            ])
            ->add('pictureFiles', FileType::class, [
                'required' => false,
                'multiple' => true,
                'label' => 'Des photos ?',
                'label_attr' => [
                    'data-browse' => 'Parcourir'
                ]
            ])
            ->add('tags', EntityType::class, [
                'class' => Tags::class,
                'choice_label' => 'name',
                'label' => 'Signes particuliers',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'pc-select'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
