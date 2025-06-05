<?php

namespace App\Controller;

use App\Repository\RendezVousRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminController extends AbstractController
{
    #[Route('/admin/rdv', name: 'admin_rendezvous')]
    #[IsGranted('ROLE_ADMIN')]
    public function voirRendezVous(RendezVousRepository $rendezVousRepository): Response
    {
        $rendezvous = $rendezVousRepository->findAll();

        return $this->render('admin/rdv.html.twig', [
            'rendezvous' => $rendezvous,
        ]);
    }
}