<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\DonsType;
use App\Form\CommentType;
use App\Repository\DonsRepository;
use App\Repository\CommentsRepository;
use App\Entity\Dons;
use App\Entity\Comments;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use App\Entity\Bonus;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface; 
use Flashy\Notifier\FlashyNotifier; // Import the correct FlashyNotifier class
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;




class DonController extends AbstractController
{
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }
    
    private function handleImageUpload($form, $d, $imageDir)
{
    if ($image = $form['image']->getData()) {
        $fileName = uniqid().'.'.$image->guessExtension();
        $d->setImage($fileName);
        $image->move($imageDir, $fileName);
    }
}

     #hedhi thez ll front
     #[Route('/front', name: 'app_produit')]
     public function index(): Response
     {
         return $this->render('don/index.html.twig', [
             'controller_name' => 'DonController',
         ]);
     }
 
 #hedhi thez ll back
     #[Route('/back', name: 'back')]
     public function indexb(): Response
     {
         return $this->render('base1.html.twig', [
             'controller_name' => 'DonController',
         ]);
     }

//liste back
#[Route('/fetchdb', name: 'fetchdb')]
public function fetchb(DonsRepository $repo): Response
{
    $result = $repo ->findAll();
    return $this -> render('/don/fetcha.html.twig',[
        'response' => $result
    ]);
}


    //liste front
     #[Route('/fetchd', name: 'fetchd')]
     public function fetchd(DonsRepository $repo): Response
     {
         $result = $repo ->findAll();
         return $this -> render('/don/ajouterD.html.twig',[
             'response' => $result
         ]);
     }

     #[Route('/show', name: 'show')]
public function show(DonsRepository $repo, Request $req, PaginatorInterface $paginator): Response
{
    $pagination = $paginator-> paginate(
        $repo->paginationQuery(),
        $req->query->get('page', 1),
        5
    );
    return $this->render('/don/show.html.twig', [
        'pagination' => $pagination
    ]);
}
#[Route('/addD', name: 'addD')]
public function addD(Request $req, ManagerRegistry $mr, SessionInterface $session): Response
{
    $imageDir = $this->getParameter('image_dir');
    $userRepository = $mr->getRepository(User::class);
    $user = $userRepository->find(123); // Assuming user with ID 123 exists

    if (!$user) {
        throw $this->createNotFoundException('User not found');
    }

    $d = new Dons();
    $d->setUserId($user->getId());
    $form = $this->createForm(DonsType::class, $d);
    $form->handleRequest($req);
    $bonus = null; // Initialize $bonus variable

    if ($form->isSubmitted() && $form->isValid()) {
        $this->handleImageUpload($form, $d, $imageDir);
        $data = $form->getData();

        // Increment donation count and check for bonus
        $user->setDonCount($user->getDonCount() + 1);
        if ($user->getDonCount() % 3 === 0) {
            $user->setDonCount(0);
            $bonus = new Bonus();
            $bonus->setMontant(1.0);
            $bonus->setPatient($user);
            $session->getFlashBag()->add('success', 'You received a bonus.'); // Display success message
        }

        $em = $mr->getManager();
        $em->persist($d);
        $em->persist($user);
        if ($bonus !== null) {
            $em->persist($bonus);
        }
        $em->flush();
        return $this->redirectToRoute('fetchd');
    }

    return $this->render('/don/form.html.twig', [
        'f' => $form->createView(),
    ]);
}

    #[Route('/myDonations', name: 'myDonations')]
    public function myDonations(ManagerRegistry $mr): Response
    {
        $repository = $mr->getRepository(Dons::class);
        $myDonations = $repository->findBy(['userP' => 123]);

        return $this->render('my_donations.html.twig', [
            'myDonations' => $myDonations,
        ]);
    }

     
     #[Route('/editD/{id}', name: 'editD')]
     public function editD(request $req, ManagerRegistry $mr, $id, DonsRepository $repo): Response
     {
         $imageDir = $this->parameterBag->get('image_dir');
         $don = $repo->find($id);
         $form = $this->createForm(DonsType::class, $don);
         $form->handleRequest($req);
     
         if ($form->isSubmitted() && $form->isValid()) {
             $this->handleImageUpload($form, $don, $imageDir);
     
             $em = $mr->getManager();
             $em->persist($don);
             $em->flush();
     
             return $this->redirectToRoute('fetchd');
         }
     
         return $this->render('/don/form.html.twig', [
             'f' => $form->createView()
         ]);
     }
     


    #[Route('/removeD/{id}', name: 'removeD')]
    public function removeD(DonsRepository $repo , $id, ManagerRegistry $mr): Response
    {
        $dn=$repo->find($id);
        $em=$mr->getManager();
        $em->remove($dn);
        $em->flush(); 

        return $this->redirectToRoute('fetchd');
    }

    #[Route('/detail/{id}', name: 'detail')]
    public function detail($id, DonsRepository $repo, Request $req, ManagerRegistry $mr, CommentsRepository $commentRepo): Response
    {
        $donns = $repo->find($id);
        if (!$donns) {
            return $this->redirectToRoute('show');
        }
    
        $comment = new Comments();
        $userRepository = $mr->getRepository(User::class);
        $user = $userRepository->find(123); // Assuming user with ID 123 exists
    
        // Create comment form
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($req);
    
        // Process form submission
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $content = $comment->getContent();
            $inappropriateWords = [
                'insulte', 'vulgaire', 'raciste', 'sexiste',
                'violence', 'haine', 'pornographie', 'drogue',
                'arnaque', 'spam', 'fraude',
            ]; 
    
            // Check for inappropriate words in content
            foreach ($inappropriateWords as $word) {
                if (stripos($content, $word) !== false) {
                    // Inappropriate word found, show error flash message
                    $this->addFlash('error', 'La description contient des mots inappropriés.');
                    return $this->redirectToRoute('detail', ['id' => $id]);
                }
            }
    
            // Set comment details
            $comment->setUser($user);
            $comment->setDon($donns);
            $comment->setCreatedAt(new \DateTime());
    
            // Check for parent comment
            $parentid = $req->request->get('parentid');
            if ($parentid) {
                $parentComment = $commentRepo->find($parentid);
                if ($parentComment) {
                    $parentComment->addReply($comment); // Add the reply to the parent comment
                    $comment->setParent($parentComment); // Set the parent comment
                }
            }
    
            // Save comment to database
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
    
            return $this->redirectToRoute('detail', ['id' => $id]);
        }
    
        // Render the detail page with the donation, comment form, and comments
        return $this->render('don/details.html.twig', [
            'p' => [$donns],
            'f' => $commentForm->createView(),
            'comments' => $commentRepo->findBy(['don' => $donns])
        ]);
    }
    

    #[Route('/fetchc', name: 'fetchc')]
     public function fetchc(CommentsRepository $commentRepo): Response
     {
         $result = $commentRepo ->findAll();
         return $this -> render('/don/fetchComment.html.twig',[
             'response' => $result
         ]);
     }


     #[Route('/removeC/{id}', name: 'removeC')]
public function removeComment($id, CommentsRepository $commentRepo, ManagerRegistry $mr): Response
{
    $comment = $commentRepo->find($id);

    if (!$comment) {
        throw $this->createNotFoundException('Comment not found');
    }

    $em = $mr->getManager();
    $this->deleteCommentAndReplies($comment, $em);

    return $this->redirectToRoute('fetchc');
}

private function deleteCommentAndReplies($comment, $em)
{
    // Recursively delete all replies
    foreach ($comment->getReplies() as $reply) {
        $this->deleteCommentAndReplies($reply, $em);
    }

    // Remove the comment from its parent's replies collection
    if ($parent = $comment->getParent()) {
        $parent->removeReply($comment);
    }

    // Remove the comment
    $em->remove($comment);
    $em->flush();
}

        public function searchAction(Request $request)
        {
            //the helper
            $em = $this->getDoctrine()->getManager();
            //9otlou jibli l haja hedhi
            $requestString = $request->get('q');
            //3amaliyet l recherche 
            $Dons = $em->getRepository('App\Entity\Dons')->findEntitiesByString($requestString);
            if(!$Dons) {
                $result['Dons']['error'] = "Don Not found :( ";
            } else {
                $result['Dons'] = $this->getRealEntities($Dons);
            }
            return new Response(json_encode($result));
        }

        public function getRealEntities($Dons){
            //lhne 9otlou aala kol don mawjouda jibli title wl taswira mte3ha
            foreach ($Dons as $Dons){
                $realEntities[$Dons->getId()] = [$Dons->getImage(),$Dons->getTitle()];
    
            }
            return $realEntities;
        }

    }