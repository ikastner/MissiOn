<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Entity\Freelance;
use App\Entity\Gestionnaire;
use App\Entity\Personel;
use App\Entity\Personnel;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthAuthenticator;
use App\Service\EmailService;
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
    public function register(
        Request $request,
        string $type,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        UserAuthAuthenticator $authenticator,
        Security $security,
        EntityManagerInterface $entityManager,
        EmailService $emailService
    ): Response {
        $user = new User();
        $user->setType($type);

        $roles = $user->getRoles();
        $user->setRoles($roles);

        $form = $this->createForm(RegistrationFormType::class, $user, ['user_type' => $type]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $userEmail = $form->get('email')->getData();

            switch ($type) {
                case 'personel':
                    // Récupérer l'utilisateur connecté 
                    $currentUser = $this->getUser();
                    // dd($this->getUser());
                    
                    // Vérification si l'utilisateur est un gestionnaire
                    if ($currentUser->getType() !== 'gestionnaire') {
                        throw new \Exception("Seul un gestionnaire peut créer un personnel.");
                    }
    
                    $personel = new Personel();
                    $personel->setEmail($userEmail);
                    $personel->setName($form->get('name')->getData());
                    // Association de entreprise de l'utilisateur au personnel
                    $gestionnaire = $currentUser->getGestionnaire();
                    $entreprise = $gestionnaire->getEntreprise();
                    // verification si l'entreprise 
                    if ($entreprise) {
                        $personel->setEntreprise($entreprise);
                    } else {
                        throw new \Exception("L'utilisateur connecté n'est pas associé à une entreprise.");
                    }
                    // Association du personnel à un utilisateur
                    $user->setPersonel($personel);
                    $entityManager->persist($personel);
                    break;

                case 'freelance':
                    $freelance = new Freelance();
                    $freelance->setEmail($userEmail);
                    $freelance->setTitre($form->get('freelance_titre')->getData());
                    $freelance->setBiographie($form->get('freelance_biographie')->getData());
                    $freelance->setNom($form->get('freelance_nom')->getData());
                    $freelance->setTJM($form->get('freelance_TJM')->getData());
                    $freelance->setPays($form->get('freelance_pays')->getData());
                    $freelance->setVille($form->get('freelance_ville')->getData());
                    $freelance->setDateNaissance($form->get('freelance_dateNaissance')->getData());

                    $user->setFreelance($freelance);
                    $entityManager->persist($freelance);
                    break;

                case 'gestionnaire':
                    $entreprise = new Entreprise();
                    $entreprise->setName($form->get('entreprise_nom')->getData());
                    $entreprise->setAdresse($form->get('entreprise_adresse')->getData());
                    $entreprise->setContact($form->get('entreprise_contact')->getData());

                    $gestionnaire = new Gestionnaire();
                    $gestionnaire->setEmail($userEmail);
                    $gestionnaire->setEntreprise($entreprise);

                    $user->setGestionnaire($gestionnaire);
                    $entityManager->persist($gestionnaire);
                    $entityManager->persist($entreprise);
                    break;

                default:
                    throw new \InvalidArgumentException("Type d'utilisateur non valide");
            }

            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

//            // Envoi de l'email de bienvenue
//            $emailService->sendEmail(
//                $userEmail,
//                'Création de votre compte',
//                'Bienvenue! Votre compte a été créé avec succès. Vous pouvez désormais vous connecter.'
//            );

            // Connexion automatique et redirection
            if ($type !== 'personel') {
                return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );
            }
            $this->addFlash('success', 'Un compte personnel à été créé avec succes');
            return $this->redirectToRoute('personels_list');

        }

        return $this->render($type === 'personel' ? 'website/admin/personnel/create.html.twig' : 'registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
