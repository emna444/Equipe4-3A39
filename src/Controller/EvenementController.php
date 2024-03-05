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

