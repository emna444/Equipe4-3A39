<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;


use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{       private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }
    
    #hedhi thez ll front
    #[Route('/front', name: 'app_produit')]
    public function index(): Response
    {
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
public function fetch(ProduitRepository $repo): Response
{ $result=$repo->findAll();
return $this->render('produit/list.html.twig', [
'response' => $result,
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

}