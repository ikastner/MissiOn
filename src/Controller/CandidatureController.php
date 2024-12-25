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
    public function index(): Response
    {
        return $this->render('candidature/index.html.twig', [
            'controller_name' => 'CandidatureController',
        ]);
    }

    #[Route('/mission-detail', name: 'mission_detail')]
    public function detail_mission(EntityManagerInterface $entityManager): Response
    {
        $missions = $entityManager->getRepository(Missions::class)->findAll();
        return $this->render('website/mission_detail.html.twig', [
            'missions'=>$missions,
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
            return $this->redirectToRoute('mission_detail', ['id' => $mission->getId()]);
        }

        $candidature = new Candidature();
        $candidature->setMission($mission);
        $candidature->setFreelance($freelance);
        $candidature->setAccepte(false);
        $candidature->setDate(new \DateTime());

        $entityManager->persist($candidature);
        $entityManager->flush();

        $this->addFlash('success', 'Votre candidature a été enregistrée avec succès.');

        return $this->redirectToRoute('mission_detail', ['id' => $mission->getId()]);

    }
}
