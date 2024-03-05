<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Form\ReponseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ReponseController extends AbstractController
{
    private $entityManager;
  
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        
    }


    
#[Route('/confirmation', name: 'confirmation', methods: ['GET'])]
public function confirmation(): Response
{
    return $this->render('reponse/confirmation.html.twig');
}

#[Route('/reponses', name: 'reponse_list')]
public function list(): Response
{
    $reponses = $this->getDoctrine()->getRepository(Reponse::class)->findAll();

    return $this->render('reponse/list.html.twig', [
        'reponses' => $reponses,
    ]);
}

    // #[Route('/reponse/edit/{id}', name: 'reponse_edit')]
    // public function edit(Request $request, Reponse $reponse): Response
    // {
    //     $form = $this->createForm(ReponseType::class, $reponse);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $this->entityManager->flush();

    //         $this->addFlash('success', 'Réponse modifiée avec succès.');

    //         return $this->redirectToRoute('reponse_show', ['id' => $reponse->getId()]);
    //     }

    //     return $this->render('reponse/edit.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }


}
