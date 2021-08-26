<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class PostMissingType extends AbstractType
{

    private Security $security;
    private PetsCareFormType $pc;

    public function __construct(Security $security, PetsCareFormType $pc)
    {
        $this->security = $security;
        $this->pc = $pc;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('missingPet', ChoiceType::class, [
                'label' => 'Qui à disparu ?',
                'choices' => $this->pc->getUserPetList($this->security->getUser()),
                'attr' => [
                    'class' => 'pc-select pc-input-normalize'
                ]
            ])
            ->add('location', ChoiceType::class, [
                'mapped' => false,
                'label' => 'Où l\'avez vous vu en dernier ?',
                'attr' => [
                    'class' => 'address-input'
                ]
            ])
            ->add('lastSeen', DateType::class, [
                'label' => "La dernière fois que vous l'avez vu ?",
                'required' => true,
                'widget' => 'single_text'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Décrivez le, le plus précis sont les infos, le plus de chances on à de retrouver votre compagnon',
                'required' => true,
                'attr' => [
                    'rows' => 5
                ]
            ])
            ->add('town', HiddenType::class)
            ->add('lat', HiddenType::class)
            ->add('lng', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'validation_groups' => false
        ]);
    }
}
