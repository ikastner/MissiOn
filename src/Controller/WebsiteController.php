<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WebsiteController extends AbstractController
{
    #[Route('/website', name: 'app_website')]
    public function index(): Response
    {
        return $this->render('website/index.html.twig', [
            'controller_name' => 'WebsiteController',
        ]);
    }

    #[Route('/website/gestionnaire', name: 'app_gestionnaire_view')]
    public function gestionnaire_view():Response {
        return $this->render('website/gestionnaire.html.twig', [
            'controller_name' => 'WebsiteController',
        ]);
    }

}
