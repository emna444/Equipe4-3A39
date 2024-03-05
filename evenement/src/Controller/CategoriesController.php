<?php

namespace App\Controller;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CategoriesController extends AbstractController
{
    #hedhi taffichi liste cat back
#[Route('/fetchCatback', name: 'fetchCatback')]

public function fetchCatback(CategoriesRepository $repo): Response
{ $result=$repo->findAll();
    
return $this->render('categories/backCat.html.twig', [
'response' => $result,
]);
}


    #[Route('/addCat',name:'addCat')]
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
        return $this->redirectToRoute('fetchCatback');   
    }       

        return $this->render('categories/ajouterCat.html.twig',[
            'f'=>$form->createView() //tab3th form ll twig form tab3thha + crateview 
        ]);
       
    }



    #[Route('/modifierCat/{id}',name:'modifierCat')]
public function modifierCat(int $id,Request $req,EntityManagerInterface $em):Response
{
$c = $em->getRepository(Categories::class)->find($id);
$form = $this->createForm(CategoriesType::class, $c);
$form->handleRequest($req);
if ($form->isSubmitted() && $form->isValid()) {
$em->flush();
return $this->redirectToRoute('fetchCatback');
}
return $this->render('categories/modifierCat.html.twig', [
    'f' => $form->createView(),
    ]);}




    
    #[Route('/deleteCat/{id}', name: 'deleteCat')]
    public function deleteCat(Categories $c, ManagerRegistry $mr): Response
    {
    $em = $mr->getManager();
    $em->remove($c);
    $em->flush();
    return $this->redirectToRoute('fetchCatback');}
}



