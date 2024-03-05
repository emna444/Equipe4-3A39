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
    //liste des bonus back

    //hedi l fetch mtaa l front
    #[Route('/ListeDesBonus', name: 'fetchf')]
    public function fetchf(BonusRepository $repo): Response
    {
        $result = $repo ->findAll();
        return $this -> render('/bonus/fetchf.html.twig',[
            'response' => $result
        ]);
    }
}
