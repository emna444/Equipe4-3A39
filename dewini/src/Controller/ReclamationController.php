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
use Endroid\QrCode\QrCode;
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
use Endroid\QrCode\Builder\QrCodeBuilder;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Attachment;
use Symfony\Component\Mailer\MailerInterface;




class ReclamationController extends AbstractController

{
   


    #[Route('/reclamations/filter', name: 'reclamation_filter_by_type')]
    public function filterByType(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        $type = $request->query->get('type');
    
        if ($type) {
            
            $reclamations = $reclamationRepository->findBy(['type' => $type]);
        } else {
            $reclamations = $reclamationRepository->findAll();
        }
    
        return $this->render('reclamation/list.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

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
        return $this->redirectToRoute('confirmation');
    }

    return $this->render('reclamation/new.html.twig', [
        'form' => $form->createView(),
    ]);
}
#[Route('/confirmation', name: 'confirmation', methods: ['GET'])]
    public function confirmation(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/confirmation.html.twig');
    }

    #[Route('/reclamation/{id}/qr-code', name: 'reclamation_qr_code')]
    public function showReclamationQrCode(Reclamation $reclamation): Response
    {
        // Générer le contenu du code QR (par exemple, l'ID de la réclamation)
        $qrCodeContent = $reclamation->getId();

        // Créer une instance de QrCode avec le contenu
        $qrCode = QrCodeBuilder::create()
            ->writer(new PngWriter())
            ->data($qrCodeContent)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->build();

        
        $label = new Label('ID de la réclamation', new NotoSans(20), new LabelAlignmentCenter());

        
        $qrCodeData = $qrCode->write($label);

       
        $response = new Response($qrCodeData, Response::HTTP_OK);

        
        $response->headers->set('Content-Type', 'image/png');

        return $response;
    }

  



    #[Route('/reclamation/{id}/download-pdf', name: 'download_reclamation_pdf')]
    public function downloadReclamationPdf(Reclamation $reclamation): Response
    {
        // Créer une instance de Dompdf
        $dompdf = new Dompdf();
    
        // Options facultatives, telles que la taille et l'orientation du papier
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf->setOptions($options);
    
        // Générer le contenu HTML du PDF à partir de la vue Twig
        $htmlContent = $this->renderView('reclamation/pdf_content.html.twig', [
            'reclamation' => $reclamation,
        ]);
    
        // Charger le contenu HTML dans Dompdf
        $dompdf->loadHtml($htmlContent);
    
        // Rendre le PDF
        $dompdf->render();
    
        // Générer le nom du fichier PDF à télécharger
        $fileName = 'reclamation_' . $reclamation->getId() . '.pdf';
    
        // Créer une réponse avec le contenu du PDF
        $response = new Response($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        ));
    
        return $response;
    }
    
    
    


        
    
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

    #[Route('/reclamation/delete/{id}', name: 'reclamation_delete')]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reclamation_list');
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
        return $this->render('base1.html.twig', [
            'controller_name' => 'ProduitController',
        ]);
    }

//partie back


     #[Route('/back', name: 'back')]
     public function indexbb(): Response
     {
         return $this->render('base1.html.twig');
        
        
        }

    #[Route('/back/reclamations/add', name: 'back_reclamation_add')]
    public function add(Request $request): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('back_reclamation_list');
        }

        return $this->render('back/reclamation/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/back/reclamations/edit/{id}', name: 'back_reclamation_edit')]
    public function editb(int $id, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation introuvable');
        }

        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('back_reclamation_list');
        }

        return $this->render('back/reclamation/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/back/reclamations/delete/{id}', name: 'back_reclamation_delete')]
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




    #[Route('/dashboard', name: 'reclamation_dashboard')]
    public function dashboard(ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $reclamationRepository->findAll();
$tabStats=$reclamationRepository->countNbreBYEtat();

$tabValMonth=[];
 
 
for ($i=1;$i<13;$i++){
    $yearMonth= date('Y').'-'.$i;
    if($i<10){
        $yearMonth=date('Y').'-0'.$i;
    }
   
    $tabValMonth[]= $reclamationRepository->countNbreBYMonth($yearMonth);
}
 
        return $this->render('back/dashboard.html.twig', [
            'reclamations' => $reclamations,
            'tabStats'=>$tabStats,
            'tabValMonth'=>$tabValMonth
        ]);
    }


}








