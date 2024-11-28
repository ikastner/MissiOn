<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ChoiceRegisterController extends AbstractController
{
    #[Route('/choice/register', name: 'app_choice_register')]
    public function index(): Response
    {
        return $this->render('choice_register/index.html.twig', [
            'controller_name' => 'ChoiceRegisterController',
        ]);
    }
}
