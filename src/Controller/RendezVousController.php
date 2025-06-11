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

#[Route('/rendezvous')]
class RendezVousController extends AbstractController  
{
    #[Route('/', name: 'rendezvous_index', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(RendezVousRepository $rdvRepo): Response  
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('rendezvous_admin_index');
        }
        
        return $this->redirectToRoute('rendezvous_new');
    }

    #[Route('/new', name: 'rendezvous_new', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(Request $request, EntityManagerInterface $em, RendezVousRepository $rdvRepo): Response  
    {
        $rdv = new RendezVous();
        $rdv->setUser($this->getUser());

        $form = $this->createForm(RendezVousType::class, $rdv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifiez que toutes les propriétés requises sont définies  
            if (!$rdv->getHeure() || !$rdv->getDate() || !$rdv->getPrestation()) {
                $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
                return $this->redirectToRoute('rendezvous_new');
            }

            if (!$rdvRepo->isCreneauLibre($rdv->getDate(), $rdv->getHeure(), $rdv->getDuree())) {
                $this->addFlash('error', 'Ce créneau est déjà pris.');
            } else {
                $em->persist($rdv);
                $em->flush();

                $this->addFlash('success', 'Rendez-vous pris avec succès !');
                return $this->redirectToRoute('rendezvous_new');
            }
        }

        return $this->render('rendezvous/priserdv.html.twig', [
            'form' => $form->createView(),
            'isAdmin' => false,
        ]);
    }


    #[Route('/{id}/edit', name: 'rendezvous_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, RendezVous $rendezVous, EntityManagerInterface $em, RendezVousRepository $rdvRepo): Response
    {
        $form = $this->createForm(RendezVousType::class, $rendezVous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$rdvRepo->isCreneauLibre($rendezVous->getDate(), $rendezVous->getHeure(), $rendezVous->getDuree())) {
                $this->addFlash('error', 'Ce créneau est déjà pris.');
            } else {
                $em->flush();
                $this->addFlash('success', 'Rendez-vous modifié avec succès.');
                return $this->redirectToRoute('rendezvous_admin_index');
            }
        }

        return $this->render('admin/rdv_edit.html.twig', [
            'form' => $form->createView(),
            'rendezVous' => $rendezVous,
        ]);
    }

    #[Route('/{id}/delete', name: 'rendezvous_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, RendezVous $rendezVous, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $rendezVous->getId(), $request->request->get('_token'))) {
            $em->remove($rendezVous);
            $em->flush();
            $this->addFlash('success', 'Rendez-vous supprimé avec succès.');
        }

        return $this->redirectToRoute('rendezvous_admin_index');
    }

    #[Route('/admin', name: 'rendezvous_admin_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminIndex(RendezVousRepository $rdvRepo): Response
    {
        $rendezvous = $rdvRepo->findAll();

        return $this->render('admin/rdv.html.twig', [
            'rendezvous' => $rendezvous,
        ]);
    }

    #[Route('/rendezvous/rdv/disponibilites', name: 'rendezvous_api_disponibilites', methods: ['GET'])]
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
}
