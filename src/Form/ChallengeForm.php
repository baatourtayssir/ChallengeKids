<?php

namespace App\Form;

use App\Entity\Challenge;
use App\Entity\Cours;
use App\Entity\Publication;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChallengeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('visibility')
            ->add('educationalContent', EntityType::class, [
                'class' => Publication::class,
                'choice_label' => 'id',
            ])
            ->add('mission', EntityType::class, [
                'class' => Publication::class,
                'choice_label' => 'id',
            ])
            ->add('pere', EntityType::class, [
                'class' => Challenge::class,
                'choice_label' => 'id',
            ])
            ->add('cours', EntityType::class, [
                'class' => Cours::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Challenge::class,
        ]);
    }
}
