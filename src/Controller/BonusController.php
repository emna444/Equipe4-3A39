<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\BonusType;
use App\Repository\BonusRepository;
use App\Entity\Bonus;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BonusController extends AbstractController
{
    #[Route('/bonus', name: 'app_bonus')]
    public function index(): Response
    {
        return $this->render('bonus/index.html.twig', [
            'controller_name' => 'BonusController',
        ]);
    }
    #[Route('/fetchb', name: 'fetchb')]
    public function fetchb(BonusRepository $repo): Response
    {
        $result = $repo ->findAll();
        return $this -> render('/bonus/ajouterb.html.twig',[
            'response' => $result
        ]);
    }


    #[Route('/addb', name: 'addb')]
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


    #[Route ('/editb/{id}', name:'editb')]
    public function editb(request $req, ManagerRegistry $mr, $id, BonusRepository $repo): Response
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


    #[Route('/removeb/{id}', name: 'removeb')]
    public function removeb(BonusRepository $repo , $id, ManagerRegistry $mr): Response
    {
        $dn=$repo->find($id);
        $em=$mr->getManager();
        $em->remove($dn);
        $em->flush(); 

        return $this->redirectToRoute('fetchb');
    }

    //hedi l fetch mtaa l front
    #[Route('/fetchf', name: 'fetchf')]
    public function fetchf(BonusRepository $repo): Response
    {
        $result = $repo ->findAll();
        return $this -> render('/bonus/fetchf.html.twig',[
            'response' => $result
        ]);
    }
}
