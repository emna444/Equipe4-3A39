<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\EditUserType;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
/*eya*/
use App\Entity\Reservation;
use App\Entity\Rendezvous;
use App\Form\RendezvousType;
use App\Repository\RendezvousRepository;
/*emna*/
use App\Entity\Commande;
use App\Entity\DetailCommande;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use SessionIdInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Dompdf\Dompdf as Dompdf;
use Dompdf\Options;
use App\Repository\DetailCommandeRepository;
use App\Entity\Produit;
use App\Form\ProduitType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use VictorPrdh\RecaptchaBundle\Form\ReCaptchaType;
/*wisal*/
use App\Form\DonsType;
use App\Form\CommentType;
use App\Repository\DonsRepository;
use App\Repository\CommentsRepository;
use App\Entity\Dons;
use App\Entity\Comments;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Entity\Bonus;
use App\Form\BonusType;
use App\Repository\BonusRepository;
// wiem
use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Entity\Reponse;
use App\Form\ReponseType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
// bappe
use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use App\Repository\PartenaireRepository;
use App\Entity\Partenaire;
use App\Form\PartenaireType;
USE App\Entity\Evenement;
use App\Repository\EvenementRepository;
use App\Form\EvenementType;
use DateTime;





/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{

    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @Route("/", name="accueil")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
    * @Route("/utilisateurs", name="utilisateurs")
    */
    public function userList(UserRepository $userRepository) // Changed argument name to $userRepository
    {
        return $this->render('admin/user.html.twig', [
            'users' => $userRepository->findAll(), // Changed variable name to $userRepository
        ]);
    }

    /**
     * @Route("/utilisateurs/ajouter", name="ajouter_utilisateur")
     */
    public function playeradd(Request $req,EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, ManagerRegistry $doctrine): Response
    {
       //objet à insérer
        $s=new User();
        //instancier la classe du formulaire
        $form=$this->createForm(RegistrationFormType::class, $s);
       
        //form is submitted or not + remplissage de l'objet $a
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $s->setPassword(
                $userPasswordHasher->hashPassword(
                    $s,
                    $form->get('plainPassword')->getData()
                )
            );
            $em=$doctrine->getManager();
            //créer la requête d'ajout
            $entityManager->persist($s);
            $entityManager->flush();
            return $this->redirectToRoute('admin_utilisateurs');
        }
       
        return $this->render("admin/add.html.twig", ['registrationForm'=>$form->createView()]);
    }


    /**
     * @Route("/utilisateurs/modifier/{id}", name="modifier_utilisateur")
     */
    public function editUser(User $user, Request $request)
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('admin_utilisateurs');
        }
        
        return $this->render('admin/edituser.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }

/**
     * @Route("/utilisateurs/delete/{id}", name="supprimer_utilisateur")
     */
    
    public function deleteuser(int $id, ManagerRegistry $mr):Response
    {
        $em=$mr->getManager();
        $repository = $em->getRepository(User::class); 

        $student = $repository->find($id);

        if (!$student) {
            return new Response("utilisateur introuvable", 404);
        }

        $em->remove($student);
        $em->flush();

        return $this->redirectToRoute('admin_utilisateurs');
    }

    /**
 * @Route("/stats", name="stats")
 */
public function statistiques(UserRepository $userRepository)
{
    // Récupérer les utilisateurs vérifiés et non vérifiés
    $users = $userRepository->findAll();

    $activ = 0;
    $inactiv = 0;

    // Compter les utilisateurs vérifiés et non vérifiés
    foreach ($users as $user) {
        if ($user->getIsVerified()) {
            $activ++;
        } else {
            $inactiv++;
        }
    }

    return $this->render('admin/stats.html.twig', [
        'activ' => $activ,
        'inactiv' => $inactiv,
    ]);
}


    /**
 * @Route("/recherche", name="recherche")
 */
public function recherche(Request $request, UserRepository $repo): Response
{
    $searchTerm = $request->query->get('user');
    $users= [];
    $results = $repo->findBySearchTerm($searchTerm);
    if (!$searchTerm) {
      //si si aucun nom n'est fourni on affiche tous les articles
      $users= $this->getDoctrine()->getRepository(User::class)->findAll();
    }
    // Recherchez les produits avec le terme de recherche spécifié
   
    return $this->render('admin/user.html.twig', [
        'searchTerm' => $searchTerm,
        'users' => $results,
    ]);
}
  

/*<!-- emna  -->*/



 /**
 * @Route("/affichercommande", name="affichercommande")
 */
public function affichercommande(CommandeRepository $repo): Response
{ $result=$repo->findAll();
return $this->render('commande/index.html.twig', [
'response' => $result,
]);
}


/**
 * @Route("/deletecommande/{id}", name="deletecommande")
 */
public function deletecommande(Commande $p, ManagerRegistry $mr): Response
{
$em = $mr->getManager();
$em->remove($p);
$em->flush();
return $this->redirectToRoute('admin_affichercommande');}

 /**
 * @Route("/telecharger-details-commande/{id}", name="telecharger_details_commande")
 */
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


 /**
 * @Route("/afficherdetail", name="afficherdetail")
 */
public function afficherdetail(DetailCommandeRepository $repo): Response
{ $result=$repo->findAll();
return $this->render('detail_commande/index.html.twig', [
'response' => $result,
]);
}


 /**
 * @Route("/telecharger", name="telecharger")
 */
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


/**
 * @Route("/listep", name="listep")
 */
public function fetchback(ProduitRepository $repo): Response
{ $result=$repo->findAll();
return $this->render('produit/listback.html.twig', [
'response' => $result,
]);
}

/**
 * @Route("/add", name="add")
 */
public function add(ManagerRegistry $mr, Request $req): Response
{ $photoDir = $this->parameterBag->get('photo_dir');
$s=new Produit();//1 instance update
$form=$this->createForm(ProduitType::class,$s);
$form->handleRequest($req);
if ($form->isSubmitted() && $form->isValid()) {
    $s=$form->getData();
   if($photo=$form['photo']->getData())
   {

    $fileName= uniqid().'.'.$photo->guessExtension();
    $s->setImageName($fileName);
    $photo->move($photoDir,$fileName);
   }
$em=$mr->getManager();
$em->persist($s);
$em->flush();
return $this->redirectToRoute('admin_listep');
}
return $this->render('produit/ajouter.html.twig',[
'f'=>$form->createView()]);
}


/**
 * @Route("/modifier/{id}", name="modif")
 */
public function modif(int $id,Request $req,EntityManagerInterface $em):Response
{
$p = $em->getRepository(Produit::class)->find($id);
$form = $this->createForm(ProduitType::class, $p);
$form->handleRequest($req);
if ($form->isSubmitted() && $form->isValid()) {
$em->flush();
return $this->redirectToRoute('admin_listep');
}
return $this->render('produit/modifier.html.twig', [
    'f' => $form->createView(),
    ]);}
    
/**
 * @Route("/deletep/{id}", name="deletep")
 */
public function deleteproduit(Produit $p, ManagerRegistry $mr): Response
{
$em = $mr->getManager();
$em->remove($p);
$em->flush();
return $this->redirectToRoute('admin_listep');}


// ***************wiem***************


/**
 * @Route("/reponse/delete/{id}", name="reponse_delete")
 */
public function deleteRR(Request $request, Reponse $reponse): Response
{
    if ($this->isCsrfTokenValid('delete'.$reponse->getId(), $request->request->get('_token'))) {
        $this->entityManager->remove($reponse);
        $this->entityManager->flush();

        $this->addFlash('success', 'Réponse supprimée avec succès.');
    }

    return $this->redirectToRoute('reponse_index');
}
/**
 * @Route("/reponse/new", name="reponse_new")
 */
public function newR(Request $request): Response
{
    $reponse = new Reponse();
    $form = $this->createForm(ReponseType::class, $reponse);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->entityManager->persist($reponse);
        $this->entityManager->flush();

        $this->addFlash('success', 'Réponse ajoutée avec succès.');

        // Rediriger vers la route 'confirmation'
        return new RedirectResponse($this->generateUrl('confirmation'));
    }

    return $this->render('reponse/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

/**
 * @Route("/reclamations", name="reclamation_list")
 */
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


/**
 * @Route("/reclamation/{id}/download-pdf", name="download_reclamation_pdf")
 */
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

         /**
 * @Route("/reclamations/edit/{id}", name="back_reclamation_edit")
 */
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

            return $this->redirectToRoute('reclamation_list');
        }

        return $this->render('back/reclamation/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
     /**
 * @Route("/reclamation/delete/{id}", name="reclamation_delete")
 */
    public function deleteR(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reclamation_list');
    }

    #[Route('/dashboard', name: 'reclamation_dashboard')]
         /**
 * @Route("/reclamationStat", name="reclamation_dashboard")
 */
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

// ***************bappe***************

   
 /**
 * @Route("/fetchCatback", name="fetchCatback")
 */
    public function fetchCatback(CategoriesRepository $repo): Response
    { $result=$repo->findAll();
        
    return $this->render('categories/backCat.html.twig', [
    'response' => $result,
    ]);
    }

    
    
     /**
 * @Route("/addCat", name="addCat")
 */
    public function addCat(ManagerRegistry $mr,CategoriesRepository $rep,Request $req):Response
    {   
        $c=new Categories();//1 instance    update 
        // avant cree cmd form 
        $form=$this->createForm(CategoriesType::class,$c); //methode dynamique : buiding entre 2 params 
       // $form->add('save_me',SubmitType::class); //
        $form->handleRequest($req); // methode definie recuperer les informations 
        if($form->isSubmitted()&& $form->isValid()) {
            
        $em=$mr->getManager();//3 persist+flush
        $em->persist($c);
        $em->flush();
        return $this->redirectToRoute('admin_fetchCatback');   
    }       

        return $this->render('categories/ajouterCat.html.twig',[
            'f'=>$form->createView() //tab3th form ll twig form tab3thha + crateview 
        ]);
       
    }
 
     /**
 * @Route("/modifierCat/{id}", name="modifierCat")
 */
public function modifierCat(int $id,Request $req,EntityManagerInterface $em):Response
{
$c = $em->getRepository(Categories::class)->find($id);
$form = $this->createForm(CategoriesType::class, $c);
$form->handleRequest($req);
if ($form->isSubmitted() && $form->isValid()) {
$em->flush();
return $this->redirectToRoute('admin_fetchCatback');
}
return $this->render('categories/modifierCat.html.twig', [
    'f' => $form->createView(),
    ]);}

         /**
 * @Route("/deleteCat/{id}", name="deleteCat")
 */
    public function deleteCat(Categories $c, ManagerRegistry $mr): Response
    {
    $em = $mr->getManager();    
    $em->remove($c);
    $em->flush();
    return $this->redirectToRoute('admin_fetchCatback');}

 /**
 * @Route("/fetchEVback", name="fetchEVback")
 */
    public function fetchEVback(EvenementRepository $repo): Response
    { $result=$repo->findAll();
        
    return $this->render('evenement/listEvBack.html.twig', [
    'response' => $result,
    ]);
    }

    /**
 * @Route("/addEV", name="addEV")
 */
public function addEV(ManagerRegistry $mr,EvenementRepository $rep,Request $req):Response
    {    $photoDir = $this->parameterBag->get('photo_dir');
        $ev=new Evenement();//1 instance    update 
        // avant cree cmd form 
        $form=$this->createForm(EvenementType::class,$ev); //methode dynamique : buiding entre 2 params 
        $form->handleRequest($req); // methode definie recuperer les informations 
        // validation d'aprés validation d'entité 
        if($form->isSubmitted()&& $form->isValid()) { 
            $ev=$form->getData();
            //photo
            if ( $photo=$form['photo']->getData ()){
                $fileName = uniqid().'.'.$photo->guessExtension();
            }
           $ev->setImageName($fileName);
           $photo->move($photoDir,$fileName);
        $em=$mr->getManager();//3 persist+flush
        $em->persist($ev);
        $em->flush();
        return $this->redirectToRoute('admin_fetchEVback');   
    }       

        return $this->render('Evenement/ajouterEv.html.twig',[
            'f'=>$form->createView() //tab3th form ll twig form tab3thha + crateview 
        ]);
       
    }

    /**
 * @Route("/modifierEv/{id}", name="modifierEv")
 */
    public function modifierEv(int $id,Request $req,EntityManagerInterface $em):Response
    {     $photoDir = $this->parameterBag->get('photo_dir');
    $ev = $em->getRepository(Evenement::class)->find($id);
    $form = $this->createForm(EvenementType::class, $ev);
    $form->handleRequest($req);
    if ($form->isSubmitted() && $form->isValid()) {
        // Gérer la mise à jour de la photo
        $photo = $form['photo']->getData();
    
        if ($photo) {
            $fileName = uniqid() . '.' . $photo->guessExtension();
            $photo->move($photoDir, $fileName);
    
            // Supprimer l'ancienne photo s'il y en a une
            $oldPhotoPath = $photoDir . '/uploads' . $ev->getImageName();
            if (file_exists($oldPhotoPath)) {
                unlink($oldPhotoPath);
            }
    
            $ev->setImageName($fileName);
        }
    $em->flush();
    return $this->redirectToRoute('admin_fetchEVback');
    }
    return $this->render('evenement/modifierEv.html.twig', [
        'f' => $form->createView(),
        ]);}
    
    
            /**
 * @Route("/deleteEv/{id}", name="deleteEv")
 */
        public function deleteEv(Evenement $ev, ManagerRegistry $mr): Response
        {
            $em = $mr->getManager();
        
            // Récupérer les partenaires associés à l'événement
            $partenaires = $ev->getPartenaires();
        
            // Supprimer chaque partenaire associé
            foreach ($partenaires as $partenaire) {
                $em->remove($partenaire);
            }
        
            // Supprimer l'événement lui-même
            $em->remove($ev);
        
            // Appliquer les suppressions
            $em->flush();
        
            return $this->redirectToRoute('admin_fetchEVback');
        }

              /**
             * @Route("/calEvents", name="calEvents")
             */
        public function fetch( EvenementRepository $repo): Response
        {
                $result=$repo->findAll();
                $events=$repo->findAll();
                $calev=[];
                foreach( $events as $event ) {
                    $title=$event->getNom();
                    $start=new \DateTime($event->getDateDebut()->format('Y-m-d H:i:s')); 
                    $end=new \DateTime($event->getDateFin()->format('Y-m-d H:i:s'));
                    $description=$event->getDescription();
                    $lieu=$event->getLieu();
                    $backgroundColor=$event->getBackgroundColor();
                    $textColor=$event->getTextColor();
                    $textColor=$event->getImageName();
                    $categ=$event-> getCats();
                    $calev[]=[
                        'id'=> $event->getId(),
                        'start'=> $start->format('Y-m-d H:i:s'),
                        'end'=> $end->format('Y-m-d H:i:s'),
                        'title'=> $title,
                        'description' => $event->getDescription(),
                    'backgroundColor' => $event->getBackgroundColor(),
                    'textColor' => $event->getTextColor(),
                    ' categ' => $event->getCats(),
                
                    ];
                }
                $data=json_encode($calev);
                return $this->render('evenement/calendarEv.html.twig', [
                    'resp' => $result,
                    'data' => $data 
                ]);
            }

            
            /**
             * @Route("/fetchPARback", name="fetchPARback")
             */
            public function fetchPARback(PartenaireRepository $repo): Response
            { $result=$repo->findAll();
            return $this->render('partenaire/listPARback.html.twig', [
            'response' => $result,
            ]);
            }

    
              /**
             * @Route("/addPar", name="addPar")
             */
    public function addPar(ManagerRegistry $mr,PartenaireRepository $rep,Request $req):Response
    {   
        $p=new Partenaire();//1 instance    update 
        // avant cree cmd form 
        $form=$this->createForm(PartenaireType::class,$p); //methode dynamique : buiding entre 2 params 
       // $form->add('save_me',SubmitType::class); //
        $form->handleRequest($req); // methode definie recuperer les informations 
        if($form->isSubmitted()&& $form->isValid()) {
            
        $em=$mr->getManager();//3 persist+flush
        $em->persist($p);
        $em->flush();
        return $this->redirectToRoute('admin_fetchPARback');   
    }       

        return $this->render('partenaire/ajouterPar.html.twig',[
            'f'=>$form->createView() //tab3th form ll twig form tab3thha + crateview 
        ]);
       
    }
  
     /**
 * @Route("/modifierPar/{id}", name="modifierPar")
 */
    public function modifierPar(int $id,Request $req,EntityManagerInterface $em):Response
    {
    $p = $em->getRepository(Partenaire::class)->find($id);
    $form = $this->createForm(PartenaireType::class, $p);
    $form->handleRequest($req);
    if ($form->isSubmitted() && $form->isValid()) {
    $em->flush();
    return $this->redirectToRoute('admin_fetchPARback');
    }
    return $this->render('partenaire/modifierPar.html.twig', [
        'f' => $form->createView(),
        ]);}
    
    
        #[Route('/deletePar/{id}', name: 'deletePar')]
            /**
 * @Route("/deletePar/{id}", name="deletePar")
 */
        public function deletePar(Partenaire $p, ManagerRegistry $mr): Response
        {
        $em = $mr->getManager();
        $em->remove($p);
        $em->flush();
        return $this->redirectToRoute('admin_fetchPARback');
    }
    
    


// ***************wisal***************

 /**
 * @Route("/ListeDons", name="fetchdb")
 */
public function fetchb(DonsRepository $repo): Response
{
    $result = $repo ->findAll();
    return $this -> render('/don/fetcha.html.twig',[
        'response' => $result
    ]);
}
 /**
 * @Route("/ListeBonus", name="fetchb")
 */
public function fetchbo(BonusRepository $repo): Response
{
    $result = $repo ->findAll();
    return $this -> render('/bonus/ajouterb.html.twig',[
        'response' => $result
    ]);
}



 /**
 * @Route("/addbonus", name="addb")
 */
public function addb(request $req, ManagerRegistry $mr): Response
{
    $b = new Bonus();
    $form = $this -> CreateForm(BonusType::class,$b);
    //$form->add('Ajouter', SubmitType::class);
    $form -> handleRequest($req);
    if ($form->isSubmitted()&& $form-> isValid())
    {
        $em=$mr->getManager(); 
        $em->persist($b); 
        $em->flush(); 

        return $this-> redirectToRoute('fetchb');
    }

    return $this -> render('/bonus/form.html.twig',[
        'f'=>$form-> CreateView()
    ]);
}


 /**
 * @Route("/editbonus/{id}", name="editb")
 */
public function editbb(request $req, ManagerRegistry $mr, $id, BonusRepository $repo): Response
{
    $bonusId = $repo -> find($id);
    $form = $this -> CreateForm(BonusType::class,$bonusId);
    $form->add('Ajouter', SubmitType::class);
    $form -> handleRequest($req);
    if ($form->isSubmitted())
    {
        $em=$mr->getManager(); 
        $em->persist($bonusId); 
        $em->flush(); 

        return $this-> redirectToRoute('fetchb');
    }
    return $this -> render('/bonus/form.html.twig',[
        'f'=>$form-> CreateView()
    ]);
}


 /**
 * @Route("/removeb/{id}", name="removeb")
 */
public function removeb(BonusRepository $repo , $id, ManagerRegistry $mr): Response
{
    $dn=$repo->find($id);
    $em=$mr->getManager();
    $em->remove($dn);
    $em->flush(); 

    return $this->redirectToRoute('fetchb');
}

    /**
 * @Route("/fetchc", name="fetchc")
 */
public function fetchc(CommentsRepository $commentRepo): Response
{
    $result = $commentRepo ->findAll();
    return $this -> render('/don/fetchComment.html.twig',[
        'response' => $result
    ]);
}


             /*<!-- rendez vous -->*/
    /**
 * @Route("/indexmain", name="indewmain")
 */
public function indexmain(RendezvousRepository $rendezvousRepository): Response
{
    $events = $rendezvousRepository->findAll();

    $rdvs = [];

    foreach($events as $event){
        $rdvs[] = [
            'id' => $event->getId(),
            'start' => $event->getDate()->format('Y-m-d H:i:s'),
            'title'=> "rdv ".$event->getId()
            
        ];
    }

    $data = json_encode($rdvs);

    return $this->render('rendezvous/maincalander.html.twig', ['data'=> $data]);
}






/******************calendar*******************/

    /**
 * @Route("/listeback", name="listeback")
 */
public function indexx(RendezvousRepository $rendezvousRepository): Response
{
    return $this->render('rendezvous/index.html.twig', [
        'rendezvouses' => $rendezvousRepository->findAll(),
    ]);
}


   /**
 * @Route("/back", name="rendezvous_index")
 */
public function back(RendezvousRepository $rendezvousRepository): Response
{
    return $this->render('rendezvous/index.html.twig', [
        'rendezvouses' => $rendezvousRepository->findAll(),
    ]);
}
    /**
 * @Route("/new", name="rendezvous_new")
 */
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $rendezvou = new Rendezvous();
    $form = $this->createForm(RendezvousType::class, $rendezvou);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($rendezvou);
        $entityManager->flush();

        return $this->redirectToRoute('admin_listeback', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('rendezvous/new.html.twig', [
        'rendezvou' => $rendezvou,
        'form' => $form,
    ]);
}
   /**
 * @Route("/{id}", name="rendezvous_show")
 */
public function show(Rendezvous $rendezvou): Response
{
    return $this->render('rendezvous/show.html.twig', [
        'rendezvou' => $rendezvou,
    ]);
}
  /**
 * @Route("/{id}/edit", name="rendezvous_edit")
 */
public function edit(Request $request, Rendezvous $rendezvou, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(RendezvousType::class, $rendezvou);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('admin_rendezvous_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('rendezvous/edit.html.twig', [
        'rendezvou' => $rendezvou,
        'form' => $form,
    ]);
}
 /**
 * @Route("/delete/{id}", name="rendezvous_delete", methods={"POST"})
 */
public function delete(Request $request, Rendezvous $rendezvou, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete'.$rendezvou->getId(), $request->request->get('_token'))) {
        $entityManager->remove($rendezvou);
        $entityManager->flush();
    }

    return $this->redirectToRoute('admin_rendezvous_index', [], Response::HTTP_SEE_OTHER);
}



 /**
 * @Route("/filter", name="rendezvous_filter_by_type", methods={"GET"})
 */
public function filterByTypeR(Request $request, RendezvousRepository $rendezvousRepository): Response
{
    $etat = $request->query->get('etat');
    

    if ($etat) {
        
        $rendezvous = $rendezvousRepository->findBy(['etat' => $etat]);
        return $this->render('rendezvous/filtre.html.twig', [
            'rendezvouses' => $rendezvous,
        ]);
        
    } else {
        $rendezvous = $rendezvousRepository->findAll();
    }

    return $this->render('rendezvous/filtre.html.twig', [
        'rendezvouses' => $rendezvous,
    ]);
}

}
