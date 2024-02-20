<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Evenement;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Form\EvenementType;




class EvenementController extends AbstractController
{


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
public function fetchEVfront(EvenementRepository $repo): Response
{ $result=$repo->findAll();
return $this->render('evenement/listEvFront.html.twig', [
'response' => $result,
]);
}
#ajouter evenement 
#[Route('/addEV',name:'addEV')]
    public function addEV(ManagerRegistry $mr,EvenementRepository $rep,Request $req):Response
    {   
        $ev=new Evenement();//1 instance    update 
        // avant cree cmd form 
        $form=$this->createForm(EvenementType::class,$ev); //methode dynamique : buiding entre 2 params 
        $form->handleRequest($req); // methode definie recuperer les informations 
        // validation d'aprés validation d'entité 
        if($form->isSubmitted()&& $form->isValid()) {  
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
{
$ev = $em->getRepository(Evenement::class)->find($id);
$form = $this->createForm(EvenementType::class, $ev);
$form->handleRequest($req);
if ($form->isSubmitted() && $form->isValid()) {
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
}
