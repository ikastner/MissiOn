<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Entity\Gestionnaire;
use App\Form\EntrepriseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EntrepriseController extends AbstractController
{
    #[Route('/entreprise', name: 'app_entreprise')]
    public function index(): Response
    {
        return $this->render('entreprise/index.html.twig', [
            'controller_name' => 'EntrepriseController',
        ]);
    }

    #[Route('/register-entreprise', name: 'app_register_entreprise')]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entreprise = new Entreprise();

        $form = $this->createForm(EntrepriseType::class, $entreprise);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // recuperation des attrubuts depuis le formulaire
            $gestionnaireNom = $form->get('gestionnaire_nom')->getData();
            $gestionnaireEmail = $form->get('gestionnaire_email')->getData();

            $gestionnaire = new Gestionnaire();
            // modification de gestionnaire
            $gestionnaire->setName($gestionnaireNom);
            $gestionnaire->setEmail($gestionnaireEmail);
            $gestionnaire->setEntreprise($entreprise);

            // enregistrement de l'entreprise et le gestionnaire
            $entityManager->persist($entreprise);
            $entityManager->persist($gestionnaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_website'); // Redirection après l'inscription

        }
        return $this->render('website/entrepriseResister.html.twig', [
            'form' => $form->createView(),
        ]);
        

    }
}
