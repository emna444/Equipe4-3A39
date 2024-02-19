<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\DetailCommande;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use SessionIdInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CommandeController extends AbstractController
{  

    #[Route('/commande', name: 'app_commande')]
    public function index(): Response
    { 
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }
    
    #[Route('/ajoutc', name: 'ajoutc')]
    public function addc(SessionInterface $session,ProduitRepository $produitRepository,EntityManagerInterface $em): Response
    {
        $panier = $session->get('panier', []);
    
        if (empty($panier)) {
            $this->addFlash('message', 'Votre panier est vide');
            return $this->redirectToRoute('fetch');
        } 
            $commande=new Commande();
//on remplit la commande
$commande->setReference(uniqid());



             foreach($panier as $item=>$quantity){
            $detailcommande=new DetailCommande();
            $produit=$produitRepository->find($item);
            $prix =$produit->getPrix();
            //on cree le detail de commande
            $detailcommande->setProduit($produit);
            $detailcommande->setPrix($prix);
            $detailcommande->setQuantite($quantity);
            $commande->addDetailCommande($detailcommande);
        
             }
             //persiste+flush
             $em->persist($commande);
             $em->flush();
             $session->remove('panier');
           $this->addFlash('message','Commande crée avec succès');
           return $this->redirectToRoute('app_produit');
        
    }
    #[Route('/affichercommande', name: 'affichercommande')]

    public function affichercommande(CommandeRepository $repo): Response
    { $result=$repo->findAll();
    return $this->render('commande/index.html.twig', [
    'response' => $result,
    ]);
    }
    #[Route('/deletecommande/{id}', name: 'deletecommande')]
    public function deletecommande(Commande $p, ManagerRegistry $mr): Response
    {
    $em = $mr->getManager();
    $em->remove($p);
    $em->flush();
    return $this->redirectToRoute('affichercommande');}
    
    }
    
    

