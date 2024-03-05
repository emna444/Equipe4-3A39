<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;

class ProduitController extends AbstractController
{
   
    #[Route('/fetchproduit', name: 'fetchproduit')]
public function fetch(ProduitRepository $repo): Response
{
    // Récupérer tous les produits depuis le repository
    $resultats = $repo->findAll();

    // Rendre la vue avec les résultats
    return $this->render('produit/list.html.twig', [
        'response' => $resultats,
    ]);
}
}
