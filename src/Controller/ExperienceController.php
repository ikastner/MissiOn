<?php

namespace App\Controller;

use App\Entity\Experience;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExperienceController extends AbstractController
{
    #[Route('/experience/ajout', name: 'add_experience', methods: ['POST'])]
    public function add_Experience(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $freelance = $user->getFreelance();

        $titre = $request->request->get('titre');
        $entreprise = $request->request->get('entreprise');
        $pays = $request->request->get('pays');
        $ville = $request->request->get('ville');
        $debut = $request->request->get('debut');
        $fin = $request->request->get('fin');
        $description = $request->request->get('description');

        $experience = new Experience();
        $experience->setTitre($titre);
        $experience->setEntreprise($entreprise);
        $experience->setPays($pays);
        $experience->setVille($ville);
        $experience->setDebut(new \DateTime($debut));
        $experience->setFin(new \DateTime($fin));
        $experience->setDescription($description);
        $experience->setFreelance($freelance);

        $entityManager->persist($experience);
        $entityManager->flush();

        $this->addFlash('success', 'Expérience ajoutée avec succès.');

        return $this->redirectToRoute('freelance_profile');
    }

    #[Route('/experience/supprimer/{id}', name: 'experience_delete', methods: ['POST'])]
    public function deleteCompetence(Experience $experience, EntityManagerInterface $entityManager): Response
    {
        // Suppression de la compétence
        $entityManager->remove($experience);
        $entityManager->flush();

        $this->addFlash('success', 'Expérience supprimée avec success');
        return $this->redirectToRoute('freelance_profile');
    }
}
