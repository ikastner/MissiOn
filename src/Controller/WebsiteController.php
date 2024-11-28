<?php

namespace App\Controller;

use App\Entity\Freelance;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        return $this->render('website/gestionnaire.html.twig', [
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

}
