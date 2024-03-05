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


    #[Route('/listerdv', name: 'listerdv', methods: ['GET'])]
    public function liste(RendezvousRepository $rendezvousRepository): Response
    {
        return $this->render('rendezvous/listerdv.html.twig', [
            'rendezvouses' => $rendezvousRepository->findAll(),
        ]);

/******************calendar*******************/
  /*  #[Route('/calendar', name: 'calendar', methods: ['GET'])]
    public function calendar(RendezvousRepository $rendezvousRepository): Response
    {
        return $this->render('rendezvous/maincalander.html.twig');
    }
    */


   

    }
    
}
