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
use Dompdf\Dompdf as Dompdf;
use Dompdf\Options;
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
    

    #[Route('/telecharger-details-commande/{id}', name: 'telecharger_details_commande')]
    public function telechargerDetailsCommande($id,EntityManagerInterface $entityManager): Response
    {
        $commande = $entityManager->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('La commande avec l\'ID '.$id.' n\'existe pas.');
        }

        // Rendu de la vue Twig pour les détails de la commande au format PDF
        $html = $this->renderView('commande/details_commande_pdf.html.twig', [
            'commande' => $commande,
        ]);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // Initialisation de Dompdf
        $dompdf = new Dompdf($pdfOptions);

        // Configuration du contexte de flux pour permettre l'accès aux ressources distantes sans vérification SSL
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ]
        ]);
        $dompdf->setHttpContext($context);

        // Chargement du HTML dans Dompdf
        $dompdf->loadHtml($html);

        // Définir la taille du papier et l'orientation
        $dompdf->setPaper('A4', 'portrait');

        // Rendre le PDF
        $dompdf->render();

        // Nom du fichier PDF téléchargé
        $fichier = 'detailcommande_'.$id.'.pdf';

        // Stream du PDF pour téléchargement
        $dompdf->stream($fichier, [
            'Attachment' => true
        ]);

        // Retourner une réponse (peut ne pas être nécessaire)
        return new Response();
    }
    
}

