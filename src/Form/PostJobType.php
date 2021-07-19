<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class PostJobType extends AbstractType
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', ChoiceType::class, [
                'mapped' => false,
                'label' => 'Où doit-on venir ?',
                'attr' => [
                    'class' => 'address-input'
                ]
            ])
            ->add('petsToBeWatched', ChoiceType::class, [
                'label' => 'Pour qui ?',
                'required' => true,
                'choices' => $this->getUserPetList($this->security->getUser()),
                'multiple' => true,
                'attr' => [
                    'class' => 'pc-select'
                ]
            ])
            ->add('petSittingStart', DateType::class, [
                'label' => 'A partir de quand ?',
                'required' => true,
                'widget' => 'single_text'
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Combien',
                'required' => true,
                'attr' => [
                    'min' => 1,
                    'max' => 7
                ]
            ])
            ->add('durationType', ChoiceType::class, [
                'label' => 'de temps ?',
                'required' => true,
                'choices' => $this->getChoices(Post::DURATION),
                'attr' => [
                    'class' => 'pc-select'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Une petite description ?',
                'required' => true,
                'attr' => [
                    'rows' => 5
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
