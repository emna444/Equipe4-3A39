<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;





class ReclamationController extends AbstractController

{
   



    #[Route('/reclamation/new', name: 'reclamation_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    
{
    $reclamation = new Reclamation();
    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Vérifier si la description contient des mots inappropriés
        $description = $reclamation->getDescription();
        $inappropriateWords = [
            'insulte',
            'vulgaire',
            'raciste',
            'sexiste',
            'violence',
            'haine',
            'pornographie',
            'drogue',
            'arnaque',
            'spam',
            'fraude',
        ]; 
        foreach ($inappropriateWords as $word) {
            if (stripos($description, $word) !== false) {
                // Mot inapproprié trouvé, renvoyer une erreur
                $this->addFlash('error', 'La description contient des mots inappropriés.');
                return $this->redirectToRoute('reclamation_new');
            }
        }

       
        $entityManager->persist($reclamation);
        $entityManager->flush();

        $this->addFlash('success', 'Réclamation ajoutée avec succès.');
        return $this->redirectToRoute('confirmationR');
    }

    return $this->render('reclamation/new.html.twig', [
        'form' => $form->createView(),
    ]);
}
#[Route('/confirmation', name: 'confirmationR', methods: ['GET'])]
    public function confirmation(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/confirmation.html.twig');
    }

    // #[Route('/reclamation/{id}/qr-code', name: 'reclamation_qr_code')]
    // public function showReclamationQrCode(Reclamation $reclamation): Response
    // {
    //     // Générer le contenu du code QR (par exemple, l'ID de la réclamation)
    //     $qrCodeContent = $reclamation->getId();

    //     // Créer une instance de QrCode avec le contenu
    //     $qrCode = QrCodeBuilder::create()
    //         ->writer(new PngWriter())
    //         ->data($qrCodeContent)
    //         ->encoding(new Encoding('UTF-8'))
    //         ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
    //         ->size(300)
    //         ->margin(10)
    //         ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
    //         ->build();

        
    //     $label = new Label('ID de la réclamation', new NotoSans(20), new LabelAlignmentCenter());

        
    //     $qrCodeData = $qrCode->write($label);

       
    //     $response = new Response($qrCodeData, Response::HTTP_OK);

        
    //     $response->headers->set('Content-Type', 'image/png');

    //     return $response;
    // }

  



    
    
    
    


        
    
    #[Route('/reclamation/edit/{id}', name: 'reclamation_edit')]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('reclamation_list');
        }

        return $this->render('reclamation/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/home', name: 'home')]
     public function indexf(): Response
     {
         return $this->render('base.html.twig');
        
        
        }

    // Partie Back


    #[Route('/back', name: 'back')]
    public function indexb(): Response
    {
        return $this->render('back.html.twig', [
            'controller_name' => 'ProduitController',
        ]);
    }

//partie back


     #[Route('/back', name: 'back')]
     public function indexbb(): Response
     {
         return $this->render('back.html.twig');
        
        
        }
// **************
    #[Route('/reclamations/add', name: 'back_reclamation_add')]
    public function add(Request $request): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('reclamation_list');
        }

        return $this->render('back/reclamation/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
// ********************
   

    // **************************
    #[Route('/reclamations/delete/{id}', name: 'back_reclamation_delete')]
    public function deleteBack(int $id, EntityManagerInterface $entityManager): Response
    {
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation introuvable');
        }

        // Récupérer les réponses associées à la réclamation
        $reponses = $reclamation->getReference();

        // Supprimer les réponses associées
        foreach ($reponses as $reponse) {
            $entityManager->remove($reponse);
        }

        // Supprimer la réclamation elle-même
        $entityManager->remove($reclamation);
        $entityManager->flush();

        return $this->redirectToRoute('reclamation_list');
    }



// **************************
   

}








