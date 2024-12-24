<?php

namespace App\Controller;

use App\Entity\Missions;
use App\Form\MissionsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MissionController extends AbstractController
{
    #[Route('/mission/create', name: 'create_missions')]
    public function create_mission(Request $request, EntityManagerInterface $entityManager ): Response
    {
        $mission = new Missions();

        // Associe le formulaire à l'entité
        $form = $this->createForm(MissionsType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mission->setTitle($form->get('title')->getData());
            $mission->setDescription($form->get('description')->getData());
            $mission->setDebut($form->get('debut')->getData());
            $mission->setFin($form->get('fin')->getData());
            $mission->setEstimation($form->get('estimation')->getData());
            $mission->setTjm($form->get('tjm')->getData());
            $mission->setStatus($form->get('status')->getData());
            $mission->setNiveau($form->get('niveau')->getData());
            $mission->setTailleProjet($form->get('taille_projet')->getData());

            $entityManager->persist($mission);
            $entityManager->flush();

            $this->addFlash('success', 'Mission créée avec succès.');

            return $this->redirectToRoute('missions_list'); 
        }

        return $this->render('website/admin/mission/create.html.twig', [
            'form' => $form->createView(),
        ]);
        
    }

    #[Route('/mission', name: 'missions_list')]
    public function list_missions(EntityManagerInterface $entityManager): Response
    {
        $missions = $entityManager->getRepository(Missions::class)->findAll();

        return $this->render('website/admin/mission/index.html.twig', [
            'missions' => $missions,
        ]);
    }
}
