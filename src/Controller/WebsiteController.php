<?php

namespace App\Controller;

use App\Entity\Freelance;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WebsiteController extends AbstractController
{
    #[Route('/website', name: 'app_website')]
    public function index(): Response
    {
        return $this->render('website/po.html.twig', [
            'controller_name' => 'WebsiteController',
        ]);
    }

    #[Route('/website/gestionnaire', name: 'app_gestionnaire_view')]
    public function gestionnaire_view():Response 
    {
        return $this->render('website/admin/index.html.twig', [
            'controller_name' => 'WebsiteController',
        ]);
    }

    #[Route('/', name: 'app_freelance_view')]
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

    // recupere l'utilisateur courrent 
    public function someAction(Security $security): Response
    {
        $user = $security->getUser(); // Récupère l'utilisateur connecté

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

}
