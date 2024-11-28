<?php

namespace App\Form;

use App\Entity\User;
// use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);

            // Ajoute les champs d'entreprise si le type est "gestionnaire"
            if ($options['user_type'] === 'gestionnaire') {
                $builder
                    ->add('entreprise_nom', TextType::class, [
                        'mapped' => false,
                        'label' => 'Nom de l\'entreprise',
                    ])
                    ->add('entreprise_adresse', TextType::class, [
                        'mapped' => false,
                        'label' => 'Adresse de l\'entreprise',
                    ])
                    ->add('entreprise_contact', EmailType::class, [
                        'mapped' => false,
                        'label' => 'Contact de l\'entreprise',
                    ]);
            }

            // Ajoute les champs de freelance si le type est "freelance"
            if ($options['user_type'] === 'freelance') {
                $builder
                    ->add('freelance_nom', TextType::class, [
                        'mapped' => false,
                        'label' => 'Nom',
                    ])
                    ->add('freelance_titre', TextType::class, [
                        'mapped' => false,
                        'label' => 'Titre',
                    ])
                    ->add('freelance_TJM', TextType::class, [
                        'mapped' => false,
                        'label' => 'TJM (Taux journalier moyen)',
                    ])
                    ->add('freelance_pays', TextType::class, [
                        'mapped' => false,
                        'label' => 'Pays',
                    ])
                    ->add('freelance_ville', TextType::class, [
                        'mapped' => false,
                        'label' => 'Ville',
                    ])
                    ->add('freelance_biographie', TextType::class, [
                        'mapped' => false,
                        'label' => 'Biographie',
                    ])
                    ->add('freelance_dateNaissance', DateType::class, [
                        'mapped' => false,
                        'label' => 'Date de naissance',
                    ]);
            }

            if($options['user_type'] === 'personel'){
                $builder
                    ->add('personel_nom', TextType::class, [
                        'mapped' => false,
                        'label' => 'Nom du personnel',
                    ])
                    ->add('personel_prenom', TextType::class, [
                        'mapped' => false,
                        'label' => 'Prenom du personnel',
                    ])
                    ->add('personel_adresse', TextType::class, [
                        'mapped' => false,
                        'label' => 'Adresse du personnel',
                    ])
                    ->add('personel_contact', EmailType::class, [
                        'mapped' => false,
                        'label' => 'Contact du personnel',
                    ]);
            }
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'user_type' => null, // Permet de passer le type d'utilisateur dans les options
        ]);
    }
}
