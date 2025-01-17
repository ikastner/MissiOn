<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Missions;
use App\Repository\CandidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CandidatureController extends AbstractController
{
    #[Route('/candidature', name: 'app_candidature')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $personel = $user->getPersonel();

        // Récupérer les candidatures des missions créées par le personnel connecté
        $candidatures = $entityManager->createQueryBuilder()
        ->select('c')
        ->from(Candidature::class, 'c')
        ->join('c.mission', 'm')
        ->where('m.personel = :personel')
        ->andWhere('c.accepte = :accepte')
        ->setParameter('personel', $personel)
        ->setParameter('accepte', 0)
        ->getQuery()
        ->getResult();

        return $this->render('website/admin/candidature/candidature.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }

    public function postuler(Request $request, EntityManagerInterface $entityManager, CandidatureRepository $candidatureRepository): Response
    {
        $missionId = $request->get('mission_id');
        $mission = $entityManager->getRepository(Missions::class)->find($missionId);

        if (!$mission) {
            throw $this->createNotFoundException('Mission non trouvée.');
        }

        $freelance = $this->getUser()->getFreelance();

        if (!$freelance) {
            throw $this->createAccessDeniedException('Vous devez être un freelance pour postuler.');
        }

        // Vérifiez si une candidature existe déjà
        $existingCandidature = $candidatureRepository->findOneByFreelanceAndMission($freelance->getId(), $mission->getId());
        if ($existingCandidature) {
            $this->addFlash('error', 'Vous avez déjà postulé pour cette mission.');
            return $this->redirectToRoute('missions_list_freelance', ['id' => $mission->getId()]);
        }

        $candidature = new Candidature();
        $candidature->setMission($mission);
        $candidature->setFreelance($freelance);
        $candidature->setAccepte(false);
        $candidature->setDate(new \DateTime());

        $entityManager->persist($candidature);
        $entityManager->flush();

        $this->addFlash('success', 'Votre candidature a été enregistrée avec succès.');

        return $this->redirectToRoute('missions_list_freelance', ['id' => $mission->getId()]);

    }


    public function validate(EntityManagerInterface $entityManager, int $id,): Response
    {
        $candidature = $entityManager->getRepository(Candidature::class)->find($id);

        $candidature->setAccepte(true);

        $mission = $candidature->getMission();
        $mission->setStatus('En cours');
        
        // l'ensemble des missions de même id qui ne sont pas acceptées
        $candidaturesNonAcceptees = $entityManager->getRepository(Candidature::class)->findBy([
            'mission' => $mission,
            'accepte' => false,
        ]);

        // suppression des candidatures qui le même mission
        foreach ($candidaturesNonAcceptees as $nonAcceptee) {
            $entityManager->remove($nonAcceptee);
        }

        $entityManager->persist($candidature);
        $entityManager->persist($mission);
        $entityManager->flush();

        $this->addFlash('success', 'La candidature a été validée avec succès.');

        return $this->redirectToRoute('app_candidature');

    }

    #[Route('/freelance/candidature', name: 'app_freelance_candidature')]
    public function freelance_candidature(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $freelance = $user->getFreelance();
        
        $candidatures = $entityManager->getRepository(Candidature::class)->findBy([
            'freelance' => $freelance,
        ]);

        return $this->render('website/freelance/candidature/candidature.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }
}
