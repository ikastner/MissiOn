<?php

namespace App\Form;

use App\Entity\Freelance;
use App\Entity\Missions;
use App\Entity\Personel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('description', TextareaType::class, [
                'mapped' => false,
                'label' => 'description',
            ])
            ->add('debut', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'label' => 'Date de debut',
            ])
            ->add('fin', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
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
            ->add('niveau', TextType::class, [
                'mapped' => false,
                'label' => 'Niveau requise',
            ])
            ->add('taille_projet', TextType::class, [
                'mapped' => false,
                'label' => 'Taille du projet',
            ])
            ->add('competences', TextType::class, [ 
                'mapped' => false, 
                'label' => 'Compétences requises',
                'attr' => [
                    'placeholder' => 'Ajoutez des compétences, séparées par des virgules',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Missions::class,
        ]);
    }
}
