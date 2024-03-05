<?php

namespace App\Controller;

use App\Entity\Suivi;
use App\Form\SuiviType;
use App\Repository\SuiviRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Dompdf\Dompdf;
use Dompdf\Options;



#[Route('/suivi')]
class SuiviController extends AbstractController
{
    #[Route('/', name: 'app_suivi_index', methods: ['GET'])]
    public function index(SuiviRepository $suiviRepository): Response
    {
        return $this->render('suivi/index.html.twig', [
            'suivis' => $suiviRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_suivi_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $suivi = new Suivi();
        $form = $this->createForm(SuiviType::class, $suivi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($suivi);
            $entityManager->flush();

            return $this->redirectToRoute('app_suivi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('suivi/new.html.twig', [
            'suivi' => $suivi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_suivi_show', methods: ['GET'])]
    public function show(Suivi $suivi): Response
    {
        return $this->render('suivi/show.html.twig', [
            'suivi' => $suivi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_suivi_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Suivi $suivi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SuiviType::class, $suivi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_suivi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('suivi/edit.html.twig', [
            'suivi' => $suivi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_suivi_delete', methods: ['POST'])]
    public function delete(Request $request, Suivi $suivi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$suivi->getId(), $request->request->get('_token'))) {
            $entityManager->remove($suivi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_suivi_index', [], Response::HTTP_SEE_OTHER);
    }



    /* pdf */
#[Route('/{id}/generate-pdf', name: 'generate_paiement_pdf')]
public function generatePdf($id): Response
{
    // Récupérer le paiement depuis la base de données (remplacez cette ligne par la logique réelle pour récupérer le paiement)
    $suivi = $this->getDoctrine()->getRepository(Suivi::class)->find($id);

    // Vérifier si le paiement existe
    if (!$suivi) {
        throw $this->createNotFoundException('Le suivi avec l\'identifiant '.$id.' n\'existe pas.');
    }

    // Créer une nouvelle instance de Dompdf
    $dompdf = new Dompdf();

    // Chargez le contenu HTML de votre modèle Twig de facture
    $html = $this->renderView('suivi/pdf.html.twig', [
        'suivi' => $suivi,
    ]);

    // Configurez les options de Dompdf si nécessaire

    // Chargez le contenu HTML dans Dompdf
    $dompdf->loadHtml($html);

    // Rendez le PDF
    $dompdf->render();

    // Retournez le PDF en réponse
    return new Response($dompdf->output(), 200, [
        'Content-Type' => 'application/pdf',
    ]);
}
}
