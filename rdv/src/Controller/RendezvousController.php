<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Rendezvous;
use App\Form\RendezvousType;
use App\Repository\RendezvousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




#[Route('/rendezvous')]
class RendezvousController extends AbstractController
{




/******************calendar*******************/
 
    #[Route('/indexmain', name: 'indewmain', methods: ['GET'])]
    public function indexmain(RendezvousRepository $rendezvousRepository): Response
    {
        $events = $rendezvousRepository->findAll();

        $rdvs = [];

        foreach($events as $event){
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getDate()->format('Y-m-d H:i:s'),
                'title'=> "rdv ".$event->getId()
                
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('rendezvous/maincalander.html.twig', ['data'=> $data]);
    }

    #[Route('/filter', name: 'rendezvous_filter_by_type', methods: ['GET'])]
    public function filterByType(Request $request, RendezvousRepository $rendezvousRepository): Response
    {
        $etat = $request->query->get('etat');
        
    
        if ($etat) {
            
            $rendezvous = $rendezvousRepository->findBy(['etat' => $etat]);
            return $this->render('rendezvous/filtre.html.twig', [
                'rendezvouses' => $rendezvous,
            ]);
            
        } else {
            $rendezvous = $rendezvousRepository->findAll();
        }
    
        return $this->render('rendezvous/filtre.html.twig', [
            'rendezvouses' => $rendezvous,
        ]);
    }


    #[Route('/listeback', name: 'listeback', methods: ['GET'])]
    public function index(RendezvousRepository $rendezvousRepository): Response
    {
        return $this->render('rendezvous/index.html.twig', [
            'rendezvouses' => $rendezvousRepository->findAll(),
        ]);
    }


#[Route('/listerdv', name: 'listerdv', methods: ['GET'])]
    public function liste(RendezvousRepository $rendezvousRepository): Response
    {
        return $this->render('rendezvous/listerdv.html.twig', [
            'rendezvouses' => $rendezvousRepository->findAll(),
        ]);
    }
    #[Route('/back', name: 'app_rendezvous_index', methods: ['GET'])]
    public function back(RendezvousRepository $rendezvousRepository): Response
    {
        return $this->render('rendezvous/index.html.twig', [
            'rendezvouses' => $rendezvousRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_rendezvous_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rendezvou = new Rendezvous();
        $form = $this->createForm(RendezvousType::class, $rendezvou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rendezvou);
            $entityManager->flush();

            return $this->redirectToRoute('listeback', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rendezvous/new.html.twig', [
            'rendezvou' => $rendezvou,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rendezvous_show', methods: ['GET'])]
    public function show(Rendezvous $rendezvou): Response
    {
        return $this->render('rendezvous/show.html.twig', [
            'rendezvou' => $rendezvou,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rendezvous_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rendezvous $rendezvou, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RendezvousType::class, $rendezvou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rendezvous_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rendezvous/edit.html.twig', [
            'rendezvou' => $rendezvou,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rendezvous_delete', methods: ['POST'])]
    public function delete(Request $request, Rendezvous $rendezvou, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rendezvou->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rendezvou);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rendezvous_index', [], Response::HTTP_SEE_OTHER);
    }



    
}
