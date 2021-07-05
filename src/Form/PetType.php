<?php

namespace App\Form;

use App\Entity\Pet;
use App\Entity\Tags;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Nom'
            ])
            ->add('age', IntegerType::class, [
                'required' => true,
                'label' => 'Âge',
                'attr' => [
                    'min' => 0,
                    'max' => 60
                ]
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => $this->getChoices(Pet::GENDER),
                'required' => true,
                'label' => 'Sexe'
            ])
            ->add('species', ChoiceType::class, [
                'choices' => $this->getChoices(Pet::SPECIES),
                'required' => true,
                'label' => 'Espèce'
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'Comment le/la décrireriez vous ? (Dites le 3 fois rapidement...)',
                'attr' => [
                    'placeholder' => "On sait déjà que c'est une beauté !",
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
            ->add('owned', CheckboxType::class, [
                'label' => 'C\'est mon compagnon !',
                'label_attr' => [
                    'class' => 'switch-custom'
                ],
            ])
            ->add('tags', EntityType::class, [
                'class' => Tags::class,
                'choice_label' => 'name',
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
            'data_class' => Pet::class,
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
