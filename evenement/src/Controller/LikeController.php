<?php

namespace App\Controller;

use App\Entity\Evenement;
use Doctrine\ORM\EntityManagerInterface;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    #[Route('/like/evenement/{id}', name: 'like.evenement', methods: ['GET'])]
  
    public function like(Evenement $evenement, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        

        if ($evenement->isLikedByUser($user)) {
            $evenement->removeLike($user);
            $manager->flush();

            return $this->json([
                'message' => 'Le like a été supprimé.',
                'nbLike' => $evenement->howManyLikes()
            ]);
        }

        $$evenement->addLike($user);
        $manager->flush();

        return $this->json([
            'message' => 'Le like a été ajouté.',
            'nbLike' => $evenement->howManyLikes()
        ]);
    }
}
