<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Entity\Gestionnaire;
use App\Entity\Personel;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    // #[Route('/register', name: 'app_register')] j'ai déjà définie les routes dans routes.yaml
    public function register(Request $request, 
        string $type,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        UserAuthAuthenticator $authenticator,
        Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        // Attribuer automatiquement le type d'utilisateur
        $user->setType($type);

        // Récupération des rôles basés sur le type
        $roles = $user->getRoles();
        $user->setRoles($roles); // assigne lees roles en fontion des types

        // Passez le type d'utilisateur dans les options du formulaire
        $form = $this->createForm(RegistrationFormType::class, $user, ['user_type' => $type]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // récupération de l'email de l'utilisateur pour l'enregister soit dans freelance ou gestionnaire ou personnel
            $userEmail = $form->get('email')->getData();

            // on l'enregistre en fonction des types 
            switch ($type) {
                // case 'freelance':
                //     $freelance = new Freelance();
                //     // ajout d'email du freelancer
                //     $freelance->setEmail($userEmail);
                //     // Association du freelancer à l'utilisateur
                //     $user->setFreelance($userEmail);
                //     // enregistrement du gestionnaire
                //     $entityManager->persist($freelance);
                //     break;
                case 'personel':
                    $personel = new Personel;
                    // ajout d'email du personnel
                    $personel->setEmail($userEmail);
                    // Association du personnel à l'utilisateur
                    $user->setPersonel($userEmail);
                    //  enregistrement du personnel
                    $entityManager->persist($personel);
                    break;
                case 'gestionnaire':
                    // Création de l'entreprise
                    $entreprise = new Entreprise();
                    // ajout du nom de l'entreprise
                    $entreprise->setName($form->get('entreprise_nom')->getData());
                    $entreprise->setAdresse($form->get('entreprise_adresse')->getData());
                    $entreprise->setContact($form->get('entreprise_contact')->getData());

                    // Création du gestionnaire
                        $gestionnaire = new Gestionnaire();
                        // ajout d'email du gestionnaire
                        $gestionnaire->setEmail($userEmail);
                        // association de gestionnaire à l'entreprise
                        $gestionnaire->setEntreprise($entreprise);
                    // Association du gestionnaire à l'utilisateur
                    $user->setGestionnaire($gestionnaire);
                    // enregistrement du gestionnaire
                    $entityManager->persist($gestionnaire);
                    // enregistrement de l'entreprise
                    $entityManager->persist($entreprise);
                    break;
            }


            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // Connexion automatique et redirection
            $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );

            // // Redirection en fonction du type d'utilisateur
            // return match ($type) {
            //     'gestionnaire' => $this->redirectToRoute('app_gestionnaire_view'),
            //     'personel' => $this->redirectToRoute('app_personnel_view'),
            //     default => $this->redirectToRoute('app_home')
            // };
            // switch ( $type ){
            //     // case 'freelance':
            //     //     return $this->redirectToRoute('app_freelance_view');
            //     // break;
            //     case 'gestionnaire':
            //         return $this->redirectToRoute('app_gestionnaire_view');
            //     break;
            //     case 'personel':
            //         return $this->redirectToRoute('app_personnel_view');
            //     break;
            // }

            // do anything else you need here, like send an email

            return $security->login($user, UserAuthAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
