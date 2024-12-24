<?php

namespace App\Form;

use App\Entity\Freelance;
use App\Entity\Missions;
use App\Entity\Personel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'mapped' => false,
                'label' => 'Titre',
            ])
            ->add('description', TextType::class, [
                'mapped' => false,
                'label' => 'description',
            ])
            ->add('debut', TextType::class, [
                'mapped' => false,
                'label' => 'Date de debut',
            ])
            ->add('fin', TextType::class, [
                'mapped' => false,
                'label' => 'Date de fin',
            ])
            ->add('estimation', TextType::class, [
                'mapped' => false,
                'label' => 'Estimation du Temps',
            ])
            ->add('tjm', TextType::class, [
                'mapped' => false,
                'label' => 'TJM',
            ])
            ->add('status', TextType::class, [
                'mapped' => false,
                'label' => 'status',
            ])
            ->add('niveau', TextType::class, [
                'mapped' => false,
                'label' => 'Niveau requise',
            ])
            ->add('taille_projet', TextType::class, [
                'mapped' => false,
                'label' => 'Taille du projet',
            ])
            // ->add('personel', EntityType::class, [
            //     'class' => Personel::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('freelance', EntityType::class, [
            //     'class' => Freelance::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Missions::class,
        ]);
    }
}
