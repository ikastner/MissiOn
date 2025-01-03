<?php

namespace App\Controller;

use App\Entity\Competence;
use App\Entity\Experience;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\New_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CompetenceController extends AbstractController
{
    #[Route('/competence/ajout', name: 'add_competence', methods: ['POST'])]
    public function add_competence( Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $freelance = $user->getFreelance();

        $name = $request->request->get('name');

        // Vérification si la compétence existe déjà pour ce freelance
        $existCompetence = $entityManager->getRepository(Competence::class)
        ->findOneBy(['name' => $name]);

        if ($existCompetence && $freelance->getCompetences()->contains($existCompetence)) {

            $this->addFlash('warning', 'Vous avez déjà cette compétence');
            return $this->redirectToRoute('freelance_profile'); // Redirection vers la page du profil
        }

        // Si la compétence n'existe pas, la créer et l'ajouter au freelance
        if (!$existCompetence) {
            $competence = new Competence();
            $competence->setName($name);
            $entityManager->persist($competence);
        } else {
            $competence = $existCompetence;
        }

        // Associer la compétence au freelance
        $competence->addFreelance($freelance);
        $entityManager->persist($competence);
        $entityManager->flush();

        $this->addFlash('success', 'Compétence ajoutée avec succès.');

        return $this->redirectToRoute('freelance_profile');
    }

    #[Route('/freelance/profil', name: 'freelance_profile')]
    public function profil( EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $freelance = $user->getFreelance();

        $competences = $freelance->getCompetences();

        $experiences = $entityManager->getRepository(Experience::class)->findBy([
            'freelance' => $freelance,
        ]);
    
        return $this->render('website/freelance/profile/profile.html.twig', [
            'competences' => $competences,
            'experiences' => $experiences,
        ]);
    }

    #[Route('/competence/supprimer/{id}', name: 'competence_delete', methods: ['POST'])]
    public function deleteCompetence(Competence $competence, EntityManagerInterface $entityManager): Response
    {
        // Suppression de la compétence
        $entityManager->remove($competence);
        $entityManager->flush();

        $this->addFlash('success', 'Compétence supprimée avec success');
        return $this->redirectToRoute('freelance_profile');
    }
}
