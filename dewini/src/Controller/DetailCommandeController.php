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
    #[Route('/afficherdetail', name: 'afficherdetail')]

    public function afficherdetail(DetailCommandeRepository $repo): Response
    { $result=$repo->findAll();
    return $this->render('detail_commande/index.html.twig', [
    'response' => $result,
    ]);
    }

    #[Route('/telecharger', name: 'telecharger')]
    public function telecharger(DetailCommandeRepository $repo)
    { $imagePath = $this->getParameter('kernel.project_dir') . '/public/logo.png';

        // Récupérer la liste de détails de commande depuis le repository
        $result = $repo->findAll();

        // Encodez le contenu de l'image en base64
        // Générer le contenu HTML de la liste de détails de commande
        $html = $this->renderView('detail_commande/telecharger.html.twig', [
            'response' => $result,

        ]);

        // Configuration de Dompdf
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
        $fichier = 'detailcommande.pdf';

        // Stream du PDF pour téléchargement
        $dompdf->stream($fichier, [
            'Attachment' => true
        ]);

        // Retourner une réponse (peut ne pas être nécessaire)
        return new Response();
    }
}
