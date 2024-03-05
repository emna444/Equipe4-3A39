<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EvenementRepository;
use App\Repository\CategoriesRepository; // Ajout de l'import pour CategoriesRepository
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Evenement;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Form\EvenementType;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Categories;

class EvenementController extends AbstractController
{
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

/**
     * @Route("/404", name="not_found")
     */
    public function notFound(): Response
    {
        return $this->render('404.html.twig');
    }


 #hedhi taffichi liste evenement back
#[Route('/fetchEVback', name: 'fetchEVback')]

public function fetchEVback(EvenementRepository $repo): Response
{ $result=$repo->findAll();
    
return $this->render('evenement/listEvBack.html.twig', [
'response' => $result,
]);
}




    
 



#hedhi taffichi liste ev front
#[Route('/fetchEVfront', name: 'fetchEVfront')]
public function fetchEVfront(EvenementRepository $repo, PaginatorInterface $paginator, Request $request): Response
{
    // Récupérer le mode d'affichage depuis la requête, par défaut "bySix"
    $viewMode = $request->query->get('viewMode', 'bySix');

    // Récupérer tous les événements
    $events = $repo->findAll();

    // Définir le nombre d'événements par page en fonction du mode d'affichage
    $eventsPerPage = ($viewMode === 'bySix') ? 6 : 3;

    // Paginer les événements
    $pagination = $paginator->paginate(
        $events,
        $request->query->getInt('page', 1), // Récupérer le numéro de page à partir de la requête, par défaut 1
        $eventsPerPage // Nombre d'événements par page en fonction du mode d'affichage
    );

    return $this->render('evenement/listEvFront.html.twig', [
        'response' => $pagination,
        'viewMode' => $viewMode, // Passer le mode d'affichage à la vue Twig
    ]);
}





#ajouter evenement 
#[Route('/addEV',name:'addEV')]
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
        return $this->redirectToRoute('fetchEVback');   
    }       

        return $this->render('Evenement/ajouterEv.html.twig',[
            'f'=>$form->createView() //tab3th form ll twig form tab3thha + crateview 
        ]);
       
    }


    #[Route('/modifierEv/{id}',name:'modifierEv')]
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
return $this->redirectToRoute('fetchEVback');
}
return $this->render('evenement/modifierEv.html.twig', [
    'f' => $form->createView(),
    ]);}


    #[Route('/deleteEv/{id}', name: 'deleteEv')]
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
    
        return $this->redirectToRoute('fetchEVback');
    }

    //like dislike
    #[Route('/likeEvent/{id}', name: 'like_event')]
    public function likeEvent(int $id, EntityManagerInterface $em): JsonResponse
    {
        $evenement = $em->getRepository(Evenement::class)->find($id);
        if (!$evenement) {
            return new JsonResponse(['error' => 'Événement non trouvé'], 404);
        }
    
        // Incrémenter le nombre de likes
        $evenement->setLikes($evenement->getLikes() + 1);
        $em->flush();
    
        return new JsonResponse(['likes' => $evenement->getLikes()]);
    }




    //affiche Calander et modifier
    #[Route('/calEvents', name:'calEvents')]
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


        #[Route('/search', name: 'search')]
        public function searchAction(Request $request)
            {
                //the helper
                $em = $this->getDoctrine()->getManager();
                //9otlou jibli l haja hedhi
                $requestString = $request->get('q');
                //3amaliyet l recherche 
                $Evenement= $em->getRepository('App\Entity\Evenement')->findEntitiesByString($requestString);
                if(!$$Evenement) {
                    $result['Evenement']['error'] = "evenement Not found  ";
                } else {
                    $result['Evenement'] = $this->getRealEntities($Evenement);
                }
                return new Response(json_encode($result));
            }
    
            public function getRealEntities($Evenement){
                //lhne 9otlou aala kol heber mawjouda jibli title wl taswira mte3ha
                foreach ($Evenement as $Evenement){
                    $realEntities[$Evenement->getId()] = [$Evenement->getNom()];
        
                }
                return $realEntities;
            }
       


       


            #[Route('/eventDetails/{id}', name: 'event_details')]
            public function showEventDetails(int $id, EntityManagerInterface $em): Response
            {
                $evenement = $em->getRepository(Evenement::class)->find($id);
                if (!$evenement) {
                    // Gérer le cas où l'événement n'est pas trouvé
                }
            
                return $this->render('evenement/details.html.twig', [
                    'evenement' => $evenement,
                ]);
            }

           
        
}

