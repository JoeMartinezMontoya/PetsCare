<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class PostMissingType extends AbstractType
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('missingPet', ChoiceType::class, [
                'label' => 'Qui à disparu ?',
                'choices' => $this->getUserPetList($this->security->getUser()),
                'attr' => [
                    'class' => 'pc-select pc-input-normalize'
                ]
            ])
            ->add('location', TextType::class, [
                'label' => 'Où était-ce ?'
            ])
            ->add('lastSeen', DateType::class, [
                'label' => "La dernière fois que vous l'avez vu ?",
                'widget' => 'single_text'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Décrivez le, le plus précis sont les infos, le plus de chances on à de retrouver votre compagnon',
                'attr' => [
                    'rows' => 5
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
