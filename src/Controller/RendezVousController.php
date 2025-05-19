<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Form\RendezVousType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RendezVousController extends AbstractController
{
    #[Route('/rendezvous', name: 'app_rendezvous')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function prendreRendezVous(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rdv = new RendezVous();
        $rdv->setUser($this->getUser()); // On associe l'utilisateur connecté

        $form = $this->createForm(RendezVousType::class, $rdv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rdv);
            $entityManager->flush();

            $this->addFlash('success', 'Rendez-vous pris avec succès !');
            return $this->redirectToRoute('app_rendezvous');
        }

        return $this->render('rendezvous.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}