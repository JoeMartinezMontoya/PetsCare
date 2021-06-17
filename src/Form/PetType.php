<?php

namespace App\Form;

use App\Entity\Pet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                'label' => "C'est quoi son p'tit nom ?",
                'required' => true,
                'help' => "Veuillez laisser le champs ci-dessus vide si il s'agit d'un animal égaré"
            ])
            ->add('age', IntegerType::class, [
                'required' => true,
                'label' => "Il a quel âge ?",
                'attr' => [
                    'min' => 0,
                    'max' => 60
                ]
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => $this->getGender(),
                'label' => "Femelle ou Mâle ?",
                'placeholder' => 'Ou Hermaphrodite..',
                'required' => true
            ])
            ->add('species', ChoiceType::class, [
                'choices' => $this->getSpecies(),
                'label' => "C'est un quoi ?",
                'placeholder' => 'Choisissez son espèce',
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Comment le/la décrireriez vous ? (Dites le 3 fois rapidement...)',
                'attr' => [
                    'placeholder' => "On sait déjà que c'est une beauté !",
                ],
                'required' => false
            ])
            ->add('owned', CheckboxType::class, [
                'label' => "C'est mon compagnon !",
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pet::class,
        ]);
    }

    public function getSpecies(): array
    {
        $choices = Pet::SPECIES;
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

    public function getGender(): array
    {
        $choices = Pet::GENDER;
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
