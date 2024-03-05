<?php

namespace App\Controller;
use App\Entity\Rendezvous;
use App\Entity\user;
use App\Entity\Medecin;
use App\Entity\Reservation;

use App\Form\ReservationType;

use App\Repository\MedecinRepository;

use App\Repository\RendezvousRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter; // Ajoutez cette ligne

use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }
    #[Route('/home', name: 'home', methods: ['GET'])]
    public function home(ReservationRepository $reservationRepository): Response
    {
        
        return $this->render('base.html.twig');
    }

    #[Route('/confirmation', name: 'confirmation', methods: ['GET'])]
    public function confirmation(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/confirmation.html.twig');
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,RendezvousRepository $rendezvousRepository): Response
    {
        
        $reservation = new Reservation();
        $availableRendezvous = $rendezvousRepository->findAvailableRendezvous();

        $form = $this->createForm(ReservationType::class, $reservation, ['available_rendezvous' => $availableRendezvous]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rendezvous = $reservation->getRendezvous();
            
            if ($rendezvous) {
                $rendezvous->setEtat(1);
                $entityManager->persist($rendezvous); // Persistez les changements du rendezvous
            }
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('confirmation', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
    /****** stati***********/
    #[Route('/chart', name: 'chart')]
    public function chartData(RendezvousRepository $repository): Response
    {
        $reservations = $repository->findAll();
        $medecinCounts = [];
        // Count the number of reservations for each medecin
        foreach ($reservations as $reservation) {
            $medecin = $reservation->getMedecin();
    
            if ($medecin) {
                $medecinNomPrenom = $medecin->getNom() . ' ' . $medecin->getPrenom();
    
                if (!isset($medecinCounts[$medecinNomPrenom])) {
                    $medecinCounts[$medecinNomPrenom] = 0;
                }
    
                $medecinCounts[$medecinNomPrenom]++;
            }
        }
        // Prepare data for the chart
        $chartData = [];
        foreach ($medecinCounts as $medecin => $count) {
            $chartData[] = ['name' => $medecin, 'y' => $count];
        }
    
        return $this->render('reservation/chart.html.twig', [
            'data' => json_encode(array_values($chartData)),
        ]);
    }




}
