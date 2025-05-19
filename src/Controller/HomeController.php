<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

 class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/rendez-vous', name: 'app_rendez_vous')]
    public function prendreRendezVous(): Response {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_register');
        }
        return $this->render('rendezvous.html.twig');
    }
    
}
