<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UpdateProfileController extends AbstractController
{
    #[Route('/modifier/profile', name: 'update_profile')]
    public function index(): Response
    {
        return $this->render('website/freelance/profile/update_profile.html.twig', [
            
        ]);
    }
    
    #[Route('/modifier/information-personnel', name: 'register_update_profile')]
    public function modifierInformations(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        $freelance = $user->getFreelance(); // Suppose que chaque utilisateur a un objet Freelance associé

        if (!$freelance) {
            throw $this->createNotFoundException("Aucune information freelance trouvée pour cet utilisateur.");
        }

        // Récupérer les données POST
        $nom = $request->request->get('nom');
        $email = $request->request->get('email');
        $titre = $request->request->get('titre');
        $tjm = $request->request->get('tjm');
        $pays = $request->request->get('pays');
        $ville = $request->request->get('ville');
        $biographie = $request->request->get('biographie');
        $dateNaissance = $request->request->get('dateNaissance');

        // Mettre à jour les informations
        $freelance->setNom($nom ?: $freelance->getNom()); // Conserve la valeur actuelle si le champ est vide
        $freelance->setEmail($email ?: $freelance->getEmail()); // Conserve la valeur actuelle si le champ est vide
        $user->setEmail($email ?: $user->getEmail());
        $freelance->setTitre($titre ?: $freelance->getTitre());
        $freelance->setTJM($tjm ?: $freelance->getTJM());
        $freelance->setPays($pays ?: $freelance->getPays());
        $freelance->setVille($ville ?: $freelance->getVille());
        $freelance->setBiographie($biographie ?: $freelance->getBiographie());

        // Convertir la date au bon format
        if ($dateNaissance) {
            $freelance->setDateNaissance(new \DateTime($dateNaissance));
        }

        // Sauvegarder les changements dans la base de données
        $entityManager->persist($freelance);
        $entityManager->persist($user); // Si vous modifiez aussi l'utilisateur
        $entityManager->flush();

        // Ajouter un message flash pour informer l'utilisateur
        $this->addFlash('success', 'Vos informations personnelles ont été mises à jour.');

        // Rediriger vers la page de profil (ou une autre page)
        return $this->redirectToRoute('freelance_profile');
    }


    // Pour les admins
    #[Route('/admin/profil', name: 'admin_profile')]
    public function profil( EntityManagerInterface $entityManager): Response
    {
    
        return $this->render('website/admin/profile/profile.html.twig', [
        ]);
    }

    #[Route('/admin/modifier/profile', name: 'admin_update_profile')]
    public function update_profil(): Response
    {
        return $this->render('website/admin/profile/update_profile.html.twig', [
            
        ]);
    }

    #[Route('/admin/modifier/information-personnel', name: 'admin_register_update_profile')]
    public function admin_update_profil( Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour modifier vos informations.');
        }

        if ($this->isGranted('ROLE_PERSONNEL')) {
            $nom = $request->request->get('nom');
            $email = $request->request->get('email');

            $personel = $user->getPersonel();
            $personel->setName($nom ?: $personel->getName()); // Conserve la valeur actuelle si le champ est vide
            $personel->setEmail($email ?: $personel->getEmail());
            $user->setEmail($email?: $user->getEmail());

            $entityManager->persist($user);
            $entityManager->persist($personel);
        }
        elseif ($this->isGranted('ROLE_GESTIONNAIRE')) {
            $nom = $request->request->get('nom');
            $email = $request->request->get('email');
            $entreprise_nom = $request->request->get('entreprise_nom');
            $entreprise_adresse = $request->request->get('entreprise_adresse');
            $entreprise_contact = $request->request->get('entreprise_contact');

            $gestionnaire = $user->getGestionnaire();
            $gestionnaire->setName($nom?: $gestionnaire->getName());
            $gestionnaire->setEmail($email?: $gestionnaire->getEmail());
            $user->setEmail($email?: $user->getEmail());

            $entreprise = $gestionnaire->getEntreprise();
            $entreprise->setName($entreprise_nom?: $entreprise->getName());
            $entreprise->setAdresse($entreprise_adresse?: $entreprise->getAdresse());
            $entreprise->setContact($entreprise_contact?: $entreprise->getContact());

            $entityManager->persist($user);
            $entityManager->persist($gestionnaire);
            $entityManager->persist($entreprise);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Vos informations ont été mises à jour avec succès.');
        return $this->redirectToRoute('admin_profile');
    
    }
}
