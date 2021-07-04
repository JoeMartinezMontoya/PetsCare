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
            ->add('location', TextType::class, [
                'label' => 'Où doit-on venir ?',
            ])
            ->add('petsToBeWatched', ChoiceType::class, [
                'label' => 'Pour qui ?',
                'choices' => $this->getUserPetList($this->security->getUser()),
                'multiple' => true,
                'attr' => [
                    'class' => 'pc-select'
                ]
            ])
            ->add('petSittingStart', DateType::class, [
                'label' => 'A partir de quand ?',
                'widget' => 'single_text'
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
                'label' => 'Une petite description ?',
                'attr' => [
                    'rows' => 5
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
