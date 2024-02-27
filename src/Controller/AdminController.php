<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\EditUserType;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
    * @Route("/utilisateurs", name="utilisateurs")
    */
    public function userList(UserRepository $userRepository) // Changed argument name to $userRepository
    {
        return $this->render('admin/user.html.twig', [
            'user' => $userRepository->findAll(), // Changed variable name to $userRepository
        ]);
    }

    /**
     * @Route("/utilisateurs/ajouter", name="ajouter_utilisateur")
     */
    public function playeradd(Request $req,EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, ManagerRegistry $doctrine): Response
    {
       //objet à insérer
        $s=new User();
        //instancier la classe du formulaire
        $form=$this->createForm(RegistrationFormType::class, $s);
       
        //form is submitted or not + remplissage de l'objet $a
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $s->setPassword(
                $userPasswordHasher->hashPassword(
                    $s,
                    $form->get('plainPassword')->getData()
                )
            );
            $em=$doctrine->getManager();
            //créer la requête d'ajout
            $entityManager->persist($s);
            $entityManager->flush();
            return $this->redirectToRoute('admin_utilisateurs');
        }
       
        return $this->render("admin/add.html.twig", ['registrationForm'=>$form->createView()]);
    }


    /**
     * @Route("/utilisateurs/modifier/{id}", name="modifier_utilisateur")
     */
    public function editUser(User $user, Request $request)
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('admin_utilisateurs');
        }
        
        return $this->render('admin/edituser.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }

/**
     * @Route("/utilisateurs/delete/{id}", name="supprimer_utilisateur")
     */
    
    public function delete(int $id, ManagerRegistry $mr):Response
    {
        $em=$mr->getManager();
        $repository = $em->getRepository(User::class); 

        $student = $repository->find($id);

        if (!$student) {
            return new Response("utilisateur introuvable", 404);
        }

        $em->remove($student);
        $em->flush();

        return $this->redirectToRoute('admin_utilisateurs');
    }

    /**
 * @Route("/stats", name="stats")
 */
public function statistiques(UserRepository $userRepository)
{
    // Récupérer les utilisateurs vérifiés et non vérifiés
    $users = $userRepository->findAll();

    $activ = 0;
    $inactiv = 0;

    // Compter les utilisateurs vérifiés et non vérifiés
    foreach ($users as $user) {
        if ($user->getIsVerified()) {
            $activ++;
        } else {
            $inactiv++;
        }
    }

    $villes = $userRepository->findAll();

        $vil = [];

        foreach($villes as $ville){
            $vil[] = $ville->getVille();
        }

    return $this->render('admin/stats.html.twig', [
        'activ' => $activ,
        'inactiv' => $inactiv,
        'vil' => json_encode($vil),
    ]);
}

    

}
