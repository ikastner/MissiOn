<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UpdateProfileController extends AbstractController
{
    #[Route('/update/profile', name: 'app_update_profile')]
    public function index(): Response
    {
        return $this->render('update_profile/index.html.twig', [
            'controller_name' => 'UpdateProfileController',
        ]);
    }
}
