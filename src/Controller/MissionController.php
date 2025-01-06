<?php

namespace App\Controller;

use App\Entity\Competence;
use App\Entity\Missions;
use App\Entity\Personel;
use App\Form\MissionsType;
use App\Repository\MessagesRepository;
use App\Repository\MissionsRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MissionController extends AbstractController
{
    #[Route('/admin/mission/create', name: 'create_missions')]
    public function create_mission(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mission = new Missions();

        // Associe le formulaire à l'entité
        $form = $this->createForm(MissionsType::class, $mission);
        $form->handleRequest($request);

        $personel = $this->getUser()->getPersonel();

        if (!$personel) {
            throw $this->createAccessDeniedException('Vous devez être un personnel pour créer un mission.');
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $mission->setPersonel($personel);
            $mission->setTitle($form->get('title')->getData());
            $mission->setDescription($form->get('description')->getData());
            $mission->setDebut($form->get('debut')->getData());
            $mission->setFin($form->get('fin')->getData());
            $mission->setEstimation($form->get('estimation')->getData());
            $mission->setTjm($form->get('tjm')->getData());
            $mission->setStatus('attente');
            $mission->setNiveau($form->get('niveau')->getData());
            $mission->setTailleProjet($form->get('taille_projet')->getData());

            $competencesInput = $form->get('competences')->getData();

            // transformer les compétences en tableau
            $competenceNames = array_map('trim', explode(',', $competencesInput));

            foreach ($competenceNames as $name) {
                $competence = new Competence();
                $competence->setName($name);

                $competence->addMission($mission);
                $entityManager->persist($competence);
            }

            $entityManager->persist($mission);
            $entityManager->flush();

            $this->addFlash('success', 'Mission créée avec succès.');

            return $this->redirectToRoute('missions_list');
        }

        return $this->render('website/admin/mission/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/mission', name: 'missions_list')]
    public function list_missions(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        //  Si l'utilisateur est un gestionnaire
        if (in_array('ROLE_GESTIONNAIRE', $user->getRoles())) {
            // Récupération du Gestionnaire associée
            $gestionnaire = $user->getGestionnaire();

            // Récupérer toutes les missions de l'entreprise associée au gestionnaire
            $entreprise = $gestionnaire->getEntreprise();

            // Récupérer tous les personnels de l'entreprise
            $personels = $entityManager->getRepository(Personel::class)->findBy([
                'entreprise' => $entreprise,
            ]);

            // Récupérer les missions créées par ces personnels
            $missions = $entityManager->getRepository(Missions::class)->createQueryBuilder('m')
                ->where('m.personel IN (:personels)')
                ->setParameter('personels', $personels)
                ->getQuery()
                ->getResult();
        } else {
            // Si l'utilisateur est un personnel
            $personel = $user->getPersonel();

            // Récupérer uniquement les missions du personnel connecté
            $missions = $entityManager->getRepository(Missions::class)->findBy([
                'personel' => $personel,
            ]);
        }
        return $this->render('website/admin/mission/index.html.twig', [
            'missions' => $missions,
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/freelance/mission', name: 'missions_list_freelance')]
    public function list_missions_freelance(EntityManagerInterface $entityManager, MessagesRepository $messagesRepo): Response
    {
        $user = $this->getUser();
        $missions = $entityManager->getRepository(Missions::class)->findAll();
        $existingConversation = false;

        foreach ($missions as $mission) {
            $personel = $mission->getPersonel();
            $userId = $personel ? $personel->getUser()->getId() : null;
            $mission->userId = $userId; // Ajouter la clé userId à l'objet mission

            if ($user && $userId) {
                $existingConversation = $messagesRepo->getConversationIdBetweenUsers($user->getId(), $userId);
                if ($existingConversation) {
                    break;
                }
            }
        }

        return $this->render('website/freelance/mission/missions.html.twig', [
            'missions' => $missions,
            'existingConversation' => $existingConversation,
        ]);
    }

    #[Route('/missions/filter', name: 'missions_filter')]
public function filterMissions(Request $request, MissionsRepository $missionRepository, MessagesRepository $messagesRepo): Response
{
    $user = $this->getUser();
    $filters = [
        'tjm' => $request->query->get('tjm'),
        'niveau' => $request->query->get('niveau'),
        'search' => $request->query->get('search'),
        'competences' => $request->query->get('competences')
    ];

    // Si le champ des compétences contient des valeurs, on le transforme en un tableau
    if (!empty($filters['competences'])) {
        $filters['competences'] = array_map('trim', explode(',', $filters['competences']));
    } else {
        $filters['competences'] = [];
    }

    $missions = $missionRepository->findByFilters($filters);
    $existingConversation = false;

    foreach ($missions as $mission) {
        $personel = $mission->getPersonel();
        $userId = $personel ? $personel->getUser()->getId() : null;
        $mission->userId = $userId; // Ajouter la clé userId à l'objet mission

        if ($user && $userId) {
            $existingConversation = $messagesRepo->getConversationIdBetweenUsers($user->getId(), $userId);
            if ($existingConversation) {
                break;
            }
        }
    }

    return $this->render('website/freelance/mission/missions.html.twig', [
        'missions' => $missions,
        'existingConversation' => $existingConversation,
    ]);
}

    #[Route('/missions/{id}/detail', name: 'mission_detail')]
    public function detail($id, MissionsRepository $missionRepository): Response
    {
        // Récupérer la mission par son ID
        $mission = $missionRepository->find($id);

        return $this->render('website/freelance/mission/detail_mission.html.twig', [
            'mission' => $mission,
        ]);
    }
}