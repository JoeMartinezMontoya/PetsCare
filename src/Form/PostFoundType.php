<?php

namespace App\Form;

use App\Entity\Pet;
use App\Entity\Post;
use App\Entity\Tags;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostFoundType extends AbstractType
{
    private PetsCareFormType $pc;

    public function __construct(PetsCareFormType $pc)
    {
        $this->pc = $pc;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', ChoiceType::class, [
                'mapped' => false,
                'label' => 'Où l\'avez vous trouver ?',
                'attr' => [
                    'class' => 'address-input'
                ]
            ])
            ->add('species', ChoiceType::class, [
                'label' => "Qu'est ce que c'est ?",
                'choices' => $this->pc->getChoices(Pet::SPECIES)
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Décrivez le',
                'required' => true,
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
            ])
            ->add('town', HiddenType::class)
            ->add('lat', HiddenType::class)
            ->add('lng', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'validation_groups' => false
        ]);
    }
}
