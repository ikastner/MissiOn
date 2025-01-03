<?php

namespace App\Controller;

use App\Entity\Freelance;
use App\Repository\FreelanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WebsiteController extends AbstractController
{
    // #[Route('/website', name: 'app_website')]
    // public function index(): Response
    // {
    //     return $this->render('website/po.html.twig', [
    //         'controller_name' => 'WebsiteController',
    //     ]);
    // }

    // #[Route('/gestionnaire', name: 'app_gestionnaire_view')]
    // public function gestionnaire_view():Response 
    // {
    //     return $this->render('website/admin/index.html.twig', [
    //         'controller_name' => 'WebsiteController',
    //     ]);
    // }

    #[Route('/', name: 'app_home')]
    public function freelance_view():Response
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

    #[Route('/freelance', name: 'app_freelance')]
    public function freelance(): Response
    {
        return $this->render('website/freelance/index.html.twig', [
            'controller_name' => 'WebsiteController',
        ]);
    }

    #[Route('/admin/find-freelance', name: 'app_find_freelance')]
    public function findFreelance( Request $request, FreelanceRepository $freelanceRepository): Response
    {
        $filters = [
            'TJM' => $request->query->get('TJM'),
            'pays' => $request->query->get('pays'),
            'ville' => $request->query->get('ville'),
            'search' => $request->query->get('search'),
        ];

        $freelances = $freelanceRepository->findByFilters($filters);

        return $this->render('website/admin/find_freelance/search_freelance.html.twig', [
            'freelances' => $freelances,
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

    // #[Route('/freelance/profile', name: 'freelance_profile')]
    // public function profil() :Response
    // {
    //     return $this->render('/website/freelance/profile/profile.html.twig', [
    //         // 'freelance' => $user->getFreelance(),
    //     ]);
    // }

}
