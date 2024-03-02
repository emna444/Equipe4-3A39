<?php

namespace App\Controller;

use App\Entity\Avis;
use Symfony\Component\HttpFoundation\Request;


use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\AvisRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use VictorPrdh\RecaptchaBundle\Form\ReCaptchaType;

class ProduitController extends AbstractController
{       private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }
    
    #hedhi thez ll front
    #[Route('/produit', name: 'app_produit')]
public function index(): Response
{
    // Appel à la fonction meilleurProduit pour obtenir les détails du meilleur produit

    // Rendre la vue Twig avec les détails du meilleur produit
    return $this->render('produit/index.html.twig', [
        'controller_name' => 'ProduitController',

    ]);
}

#hedhi thez ll back
    #[Route('/back', name: 'back')]
    public function indexb(): Response
    {
        return $this->render('base1.html.twig', [
            'controller_name' => 'ProduitController',
        ]);
    }
    #hedhi taffichi liste produit front
    #[Route('/fetch', name: 'fetch')]
    public function fetch(EntityManagerInterface $entityManager, AvisRepository $avisRepository,ProduitRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupérer tous les produits depuis le repository
        $resultats = $repo->findAll();
    
        // Paginer les résultats
        $pagination = $paginator->paginate(
            $resultats, // Requête contenant les données à paginer
            $request->query->getInt('page', 1), // Numéro de page par défaut
            6 // Nombre d'éléments par page
        );
    
        // Appel de la fonction pour récupérer le meilleur produit
        $meilleurProduitDetails = $this->meilleurProduit($entityManager, $avisRepository);

        // Rendre la vue avec les résultats paginés et le meilleur produit
        return $this->render('produit/list.html.twig', [
            'response' => $pagination,
            'meilleurProduit' => $meilleurProduitDetails['meilleurProduit'],
            'moyenneMax' => $meilleurProduitDetails['moyenneMax'],
            
        ]);
    }
    
    #hedhi taffichi liste produit back
#[Route('/fetchback', name: 'fetchback')]
public function fetchback(ProduitRepository $repo): Response
{ $result=$repo->findAll();
return $this->render('produit/listback.html.twig', [
'response' => $result,
]);
}

#[Route('/add',name:'add')]
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
return $this->redirectToRoute('fetchback');
}
return $this->render('produit/ajouter.html.twig',[
'f'=>$form->createView()]);
}


#[Route('/modifier/{id}',name:'modif')]
public function modif(int $id,Request $req,EntityManagerInterface $em):Response
{
$p = $em->getRepository(Produit::class)->find($id);
$form = $this->createForm(ProduitType::class, $p);
$form->handleRequest($req);
if ($form->isSubmitted() && $form->isValid()) {
$em->flush();
return $this->redirectToRoute('fetchback');
}
return $this->render('produit/modifier.html.twig', [
    'f' => $form->createView(),
    ]);}
    
    #[Route('/delete/{id}', name: 'delete')]
public function delete(Produit $p, ManagerRegistry $mr): Response
{
$em = $mr->getManager();
$em->remove($p);
$em->flush();
return $this->redirectToRoute('fetchback');}


#[Route('/show', name: 'show')]
public function detail( Produit $p): Response
{
return $this->render('produit/show.html.twig', ['p' =>
$p]);
}
#[Route('/recherche', name: 'recherche')]
public function recherche(Request $request, ProduitRepository $repo, PaginatorInterface $paginator, EntityManagerInterface $entityManager, AvisRepository $avisRepository): Response
{
    $searchTerm = $request->query->get('q');

    if (!$searchTerm) {
        $this->addFlash('error', 'Veuillez fournir un terme de recherche.');
        return $this->redirectToRoute('fetch');
    }

    // Recherchez les produits avec le terme de recherche spécifié
    $results = $repo->findBySearchTerm($searchTerm);

    // Paginer les résultats de la recherche
    $pagination = $paginator->paginate(
        $results,
        $request->query->getInt('page', 1),
        6
    );
    $meilleurProduitData = $this->meilleurProduit($entityManager, $avisRepository);

    // Récupérer le meilleur produit et sa moyenne de notes à partir des données retournées
    $meilleurProduit = $meilleurProduitData['meilleurProduit'];
    $moyenneMax = $meilleurProduitData['moyenneMax'];

    return $this->render('produit/list.html.twig', [
        'response' => $pagination,
        'searchTerm' => $searchTerm,
        'meilleurProduit' => $meilleurProduit,
        'moyenneMax' => $moyenneMax,
    ]);
}


#[Route('/tri', name: 'tri')]
public function tri(Request $request, ProduitRepository $repo, PaginatorInterface $paginator, EntityManagerInterface $entityManager, AvisRepository $avisRepository): Response
{
    // Récupérer le critère de tri depuis la requête
    $tri = $request->query->get('tri', 'nom');

    // Vérifiez si le tri est valide
    if (!in_array($tri, ['nom', 'prix'])) {
        throw $this->createNotFoundException('Tri invalide.');
    }

    // Récupérer les résultats triés depuis le repository
    $results = $repo->findByTri($tri);

    // Paginer les résultats triés
    $pagination = $paginator->paginate(
        $results,
        $request->query->getInt('page', 1), // Numéro de page par défaut
        6 // Nombre d'éléments par page
    );
    $meilleurProduitData = $this->meilleurProduit($entityManager, $avisRepository);

    // Récupérer le meilleur produit et sa moyenne de notes à partir des données retournées
    $meilleurProduit = $meilleurProduitData['meilleurProduit'];
    $moyenneMax = $meilleurProduitData['moyenneMax'];
    return $this->render('produit/list.html.twig', [
        'response' => $pagination,
        'tri' => $tri,
        'meilleurProduit' => $meilleurProduit,
        'moyenneMax' => $moyenneMax,
    ]);
}

#[Route('/show{id}', name: 'produitdetail')]
public function showd(Produit $p = null, Request $request, EntityManagerInterface $entityManager): Response {
    if(!$p) {
        $this->addFlash('error', "Le produit n'existe pas ");
        return $this->redirectToRoute('fetch');
    }
    
    // Création d'un nouvel objet Avis
    $avis = new Avis();
    
    // Création du formulaire pour l'avis
    $form = $this->createFormBuilder($avis)
    ->add('commentaire', TextareaType::class, ['label' => 'Votre avis'])
    ->add('note', ChoiceType::class, [
        'label' => false, // Enlever l'étiquette
        'choices' => [
            'Mauvais' => 1,
            'Passable' => 2,
            'Bien' => 3,
            'Très bien' => 4,
            'Excellent' => 5,
        ],
    ])     
    ->add('captcha', ReCaptchaType::class)
    ->add('submit', SubmitType::class, ['label' => 'Publier'])
    
        ->getForm();
    // Gestion de la soumission du formulaire
$form->handleRequest($request);
if ($form->isSubmitted() && $form->isValid()) {
    $avis = $form->getData();
    $avis->setProduit($p); // Association du produit à l'avis
    
    // Récupération de la note depuis l'objet Avis
    
    // Enregistrement de l'avis dans la base de données
    $entityManager->persist($avis);
    $entityManager->flush();
    
    // Ajout d'un message flash pour confirmer la soumission de l'avis
    $this->addFlash('success', 'Votre avis a été enregistré avec succès.');
    
    // Rafraîchissement de la page pour afficher le nouvel avis
    return $this->redirectToRoute('produitdetail', ['id' => $p->getId()]);
}

    $avisProduit = $entityManager->getRepository(Avis::class)->findBy(['Produit' => $p]);

    // Rendu de la vue Twig avec le produit, le formulaire et les avis
    return $this->render('produit/show.html.twig', [
        'p' => $p,
        'form' => $form->createView(), // Passer le formulaire à Twig
        'avisProduit' => $avisProduit, // Passer les avis associés à ce produit à Twig
    ]);
}
#[Route('/filtrer-produits-par-prix', name: 'filtrer_produits_par_prix')]
public function filtrerProduitsParPrix(Request $request, ProduitRepository $produitRepository): JsonResponse
{
    // Récupérer les valeurs des paramètres de la requête
    $prixMin = $request->query->get('prixMin');
    $prixMax = $request->query->get('prixMax');

    // Filtrer les produits en fonction des prix min et max
    $produitsFiltres = $produitRepository->findByPrixRange($prixMin, $prixMax);

    // Construire un tableau des données des produits filtrés
    $data = [];
    foreach ($produitsFiltres as $produit) {
        $data[] = [
            'id' => $produit->getId(),
            'nom' => $produit->getNom(),
            'prix' => $produit->getPrix(),
            'image' => $produit->getImageName(),
            // Ajoutez d'autres attributs du produit si nécessaire
        ];
    }

    // Retourner les résultats au format JSON
    return new JsonResponse($data);
}
public function meilleurProduit(EntityManagerInterface $entityManager, AvisRepository $avisRepository): array
{
    // Récupérer tous les produits
    $produits = $entityManager->getRepository(Produit::class)->findAll();

    // Initialiser une variable pour stocker le meilleur produit
    $meilleurProduit = null;
    $moyenneMax = 0;

    // Parcourir tous les produits
    foreach ($produits as $produit) {
        // Récupérer tous les avis associés à ce produit
        $avisProduit = $avisRepository->findBy(['Produit' => $produit]);

        // Calculer la moyenne des notes pour ce produit
        $nombreAvis = count($avisProduit);
        $sommeNotes = 0;
        foreach ($avisProduit as $avis) {
            $sommeNotes += $avis->getNote();
        }
        $moyenneNotes = $nombreAvis > 0 ? $sommeNotes / $nombreAvis : 0;

        // Vérifier si la moyenne des notes pour ce produit est supérieure à la moyenne maximale trouvée jusqu'à présent
        if ($moyenneNotes > $moyenneMax) {
            $meilleurProduit = $produit;
            $moyenneMax = $moyenneNotes;
        }
    }

    // Retourner le meilleur produit et sa moyenne des notes
    return [
        'meilleurProduit' => $meilleurProduit,
        'moyenneMax' => $moyenneMax,
    ];
}



}