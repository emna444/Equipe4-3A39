<?php

namespace App\Controller;

use App\Repository\DetailCommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf as Dompdf;
use Dompdf\Options;
class DetailCommandeController extends AbstractController
{
    #[Route('/detail/commande', name: 'app_detail_commande')]
    public function index(): Response
    {
        return $this->render('detail_commande/index.html.twig', [
            'controller_name' => 'DetailCommandeController',
        ]);
    }
    
}
