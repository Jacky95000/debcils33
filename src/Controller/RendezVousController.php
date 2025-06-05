<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Form\RendezVousType;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RendezVousController extends AbstractController
{
    #[Route('/rendezvous', name: 'app_rendezvous')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function prendreRendezVous(
        Request $request,
        EntityManagerInterface $em,
        RendezVousRepository $rdvRepo
    ): Response {
        // Redirection si admin
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_rdv');
        }

        $rdv = new RendezVous();
        $rdv->setUser($this->getUser());

        $form = $this->createForm(RendezVousType::class, $rdv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           if (!$rdv->getHeure()) {
    $this->addFlash('error', 'Veuillez sélectionner une heure.');
    return $this->redirectToRoute('app_rendezvous');
}


            if (!$rdvRepo->isCreneauLibre($rdv->getDate(), $rdv->getHeure(), $rdv->getDuree())) {
                $this->addFlash('error', 'Ce créneau est déjà pris.');
            } else {
                $em->persist($rdv);
                $em->flush();

                $this->addFlash('success', 'Rendez-vous pris avec succès !');
                return $this->redirectToRoute('app_rendezvous');
            }
        }

        return $this->render('rendezvous/priserdv.html.twig', [
            'form' => $form->createView(),
            'isAdmin' => false,
        ]);
    }

    #[Route('/admin/rdv', name: 'admin_rdv')]
    #[IsGranted('ROLE_ADMIN')]
    public function listeAdmin(RendezVousRepository $rdvRepo): Response
    {
        $rendezvous = $rdvRepo->findAll();

        return $this->render('admin/rdv.html.twig', [
            'rendezvous' => $rendezvous,
        ]);
    }

    #[Route('/api/rdv/disponibilites', name: 'api_rdv_disponibilites')]
    public function getDisponibilites(RendezVousRepository $rdvRepo): JsonResponse
    {
        $rdvs = $rdvRepo->findAll();
        $data = [];

        foreach ($rdvs as $rdv) {
            $data[] = [
                'date' => $rdv->getDate()->format('Y-m-d'),
                'heure' => $rdv->getHeure()->format('H:i'),
                'duree' => $rdv->getDuree(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/admin/rdv/delete/{id}', name: 'admin_rdv_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(RendezVous $rendezVous, EntityManagerInterface $em): Response
    {
        $em->remove($rendezVous);
        $em->flush();

        $this->addFlash('success', 'Rendez-vous supprimé avec succès.');
        return $this->redirectToRoute('admin_rdv');
    }
}
