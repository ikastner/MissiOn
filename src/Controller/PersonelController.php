<?php

namespace App\Controller;

use App\Entity\Personel;
use App\Form\PersonelType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PersonelController extends AbstractController
{
    #[Route('/personel/list', name: 'personels_list')]
    public function list_personel(EntityManagerInterface $entityManager): Response
    {
        $personels = $entityManager->getRepository(Personel::class)->findAll();

        return $this->render('website/admin/personnel/index.html.twig', [
            'personels' => $personels,
        ]);
    }

    public function update(Request $request, Personel $personel, EntityManagerInterface $entityManager )
    {
        // Récupération et validation des données du formulaire
        $form = $this->createForm(PersonelType::class, $personel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Les données du formulaire sont automatiquement injectées dans l'entité $distribution
            $entityManager->flush(); // Enregistre les modifications dans la base de données
    
            
            $this->addFlash('success', 'Personnel modifiée avec succès.');
            return $this->redirectToRoute('personels_list');
        }
    
        // Si le formulaire est invalide, on affiche le formulaire avec les erreurs
        return $this->render('website/admin/personnel/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/personel/{id}/delete', name: 'personel_delete', methods: ['DELETE'])]
    public function delete(Request $request, Personel $personel, EntityManagerInterface $entityManager): Response
    {
        // Vérifie le token CSRF
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete' . $personel->getId(), $submittedToken)) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        try {
            $entityManager->remove($personel);
            $entityManager->flush();
            $this->addFlash('success', 'Personnel supprimé avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }

        return $this->redirectToRoute('personels_list');
        }
    }
