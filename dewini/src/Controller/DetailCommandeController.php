<?php

namespace App\Controller;

use App\Repository\DetailCommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailCommandeController extends AbstractController
{
    #[Route('/detail/commande', name: 'app_detail_commande')]
    public function index(): Response
    {
        return $this->render('detail_commande/index.html.twig', [
            'controller_name' => 'DetailCommandeController',
        ]);
    }
    #[Route('/afficherdetail', name: 'afficherdetail')]

    public function afficherdetail(DetailCommandeRepository $repo): Response
    { $result=$repo->findAll();
    return $this->render('detail_commande/index.html.twig', [
    'response' => $result,
    ]);
    }
}
