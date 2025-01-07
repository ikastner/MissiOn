<?php

namespace App\Controller;

use App\Entity\Freelance;
use App\Repository\FreelanceRepository;
use App\Repository\MessagesRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebsiteController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function freelance_view(): Response
    {
        return $this->render('/home/index.html.twig', [
            'controller_name' => 'WebsiteController',
        ]);
    }

    #[Route('/parcourir-freelances', name: 'app_browse_freelances')]
    public function browseFreelances(EntityManagerInterface $entityManager): Response
    {
        $freelances = $entityManager->getRepository(Freelance::class)->findAll();

        return $this->render('website/browse_freelances.html.twig', [
            'freelances' => $freelances,
        ]);
    }



    #[Route('/admin', name: 'app_admin')]
    public function admin(): Response
    {
        return $this->render('website/admin/index.html.twig', [
            'controller_name' => 'WebsiteController',
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/admin/find-freelance', name: 'app_find_freelance')]
    public function findFreelance(Request $request, FreelanceRepository $freelanceRepository, MessagesRepository $messagesRepo): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur connecté

        $filters = [
            'TJM' => $request->query->get('TJM'),
            'pays' => $request->query->get('pays'),
            'ville' => $request->query->get('ville'),
            'search' => $request->query->get('search'),
            'competences' => $request->query->get('competences')
        ];

        // Si le champ des compétences contient des valeurs, on le transforme en un tableau
        if (!empty($filters['competences'])) {
            $filters['competences'] = array_map('trim', explode(',', $filters['competences']));
        } else {
            $filters['competences'] = [];
        }

        $freelances = $freelanceRepository->findByFilters($filters);
        $existingConversation = false;

        foreach ($freelances as $freelance) {
            $userId = $freelance->getUser()->getId();
            $freelance->userId = $userId; // Ajouter la clé userId à l'objet freelance

            if ($user && $userId) {
                $existingConversation = $messagesRepo->getConversationIdBetweenUsers($user->getId(), $userId);
                if ($existingConversation) {
                    break;
                }
            }
        }

        return $this->render('website/admin/find_freelance/search_freelance.html.twig', [
            'freelances' => $freelances,
            'existingConversation' => $existingConversation,
        ]);
    }

    // recupere l'utilisateur courrent
    public function someAction(Security $security): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté

        if ($user) {
            $gestionnaire = $user->getGestionnaire();  // Si l'utilisateur est un gestionnaire
            $personel = $user->getPersonel();
            $freelance = $user->getFreelance();

            $userName = $gestionnaire ? $gestionnaire->getName() :
                ($personel ? $personel->getName() :
                    ($freelance ? $freelance->getName() : 'Utilisateur inconnu'));

            return $this->render('website/admin/layout/base.html.twig', [
                'user_name' => $userName,
                'controller_name' => 'WebsiteController',
            ]);
        }

        return $this->redirectToRoute('app_login');
    }

    #[Route('/admin/freelance/{id}/profile', name: 'detail_freelance_profile')]
    public function detail($id, FreelanceRepository $freelanceRepository)
    {
        // Récupérer la mission par son ID
        $freelance = $freelanceRepository->find($id);

        return $this->render('website/admin/find_freelance/profile_freelance.html.twig', [
            'freelance' => $freelance,
        ]);
    }

    #[Route('/freelance/{id}/profile/detail', name: 'freelance_profile_detail')]
    public function detailProfile($id, FreelanceRepository $freelanceRepository)
    {
        // Récupérer la mission par son ID
        $freelance = $freelanceRepository->find($id);

        return $this->render('website/profile_freelance.html.twig', [
            'freelance' => $freelance,
        ]);
    }
}
