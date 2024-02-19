<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Partenaire;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Form\PartenaireType;
USE App\Entity\Evenement;

class PartenaireController extends AbstractController
{
    #[Route('/fetchPARback', name: 'fetchPARback')]

public function fetchPARback(PartenaireRepository $repo): Response
{ $result=$repo->findAll();
return $this->render('partenaire/listPARback.html.twig', [
'response' => $result,
]);
}



#[Route('/addPar',name:'addPar')]
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
        return $this->redirectToRoute('fetchPARback');   
    }       

        return $this->render('partenaire/ajouterPar.html.twig',[
            'f'=>$form->createView() //tab3th form ll twig form tab3thha + crateview 
        ]);
       
    }


    #[Route('/modifierPar/{id}',name:'modifierPar')]
public function modifierPar(int $id,Request $req,EntityManagerInterface $em):Response
{
$p = $em->getRepository(Partenaire::class)->find($id);
$form = $this->createForm(PartenaireType::class, $p);
$form->handleRequest($req);
if ($form->isSubmitted() && $form->isValid()) {
$em->flush();
return $this->redirectToRoute('fetchPARback');
}
return $this->render('partenaire/modifierPar.html.twig', [
    'f' => $form->createView(),
    ]);}


    #[Route('/deletePar/{id}', name: 'deletePar')]
    public function deletePar(Partenaire $p, ManagerRegistry $mr): Response
    {
    $em = $mr->getManager();
    $em->remove($p);
    $em->flush();
    return $this->redirectToRoute('fetchPARback');}
}
