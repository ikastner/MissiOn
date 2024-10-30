<?php

namespace App\Form;

use App\Entity\Entreprise;
use App\Entity\Gestionnaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntrepriseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('adresse')
            ->add('contact', EmailType::class)
            ->add('gestionnaire_email', EmailType::class, [
                'label' => 'Email du gestionnaire',
                'mapped' => false, // Empêche la liaison avec l'entité Entreprise
                'attr' => [
                    'placeholder' => 'Entrez l\'email du gestionnaire',
                ],
            ])
            // ->add('gestionnaire_nom', EntityType::class, [
            //     'class' => Gestionnaire::class,
            //     'choice_label' => 'id',
            //     'mapped' => false, // Empêche la liaison avec l'entité Entreprise
            // ])
            ->add('gestionnaire_nom', TypeTextType::class, [
                'label' => 'Nom du gestionnaire',
                'mapped' => false, // Empêche la liaison avec l'entité Entreprise
                'attr' => [
                    'placeholder' => 'Entrez le nom du gestionnaire',
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entreprise::class,
        ]);
    }
}
