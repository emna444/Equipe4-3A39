<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Participation;

class ParticipationController extends AbstractController
{
    #[Route('/participation', name: 'app_participation')]
    public function index(): Response
    {
        return $this->render('participation/index.html.twig', [
            'controller_name' => 'ParticipationController',
        ]);
    }

/**
     * @Route("/participate/{eventId}", name="participate")
     */
    public function participate(int $eventId, Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Vous devez vous connecter pour participer.'], 403);
        }

        $evenement = $entityManager->getRepository(Evenement::class)->find($eventId);

        if (!$evenement) {
            return new JsonResponse(['error' => 'Événement non trouvé.'], 404);
        }

        // Vérifier si l'utilisateur participe déjà à cet événement
        $existingParticipation = $entityManager->getRepository(Participation::class)->findOneBy(['user' => $user, 'evenement' => $evenement]);

        if ($existingParticipation) {
            return new JsonResponse(['error' => 'Vous participez déjà à cet événement.'], 400);
        }

        // Créer une nouvelle participation
        $participation = new Participation();
        $participation->setUser($user);
        $participation->setEvenement($evenement);

        // Enregistrer la participation
        $entityManager->persist($participation);
        $entityManager->flush();

        return new JsonResponse(['success' => 'Vous avez participé à cet événement avec succès.']);
    }

    /**
     * @Route("/cancel-participation/{eventId}", name="cancel_participation")
     */
    public function cancelParticipation(int $eventId, Request $request): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Vous devez vous connecter pour annuler votre participation.'], 403);
        }

        $evenement = $entityManager->getRepository(Evenement::class)->find($eventId);

        if (!$evenement) {
            return new JsonResponse(['error' => 'Événement non trouvé.'], 404);
        }

        // Rechercher la participation de l'utilisateur à cet événement
        $participation = $entityManager->getRepository(Participation::class)->findOneBy(['user' => $user, 'evenement' => $evenement]);

        if (!$participation) {
            return new JsonResponse(['error' => 'Vous ne participez pas à cet événement.'], 400);
        }

        // Supprimer la participation
        $entityManager->remove($participation);
        $entityManager->flush();

        return new JsonResponse(['success' => 'Votre participation à cet événement a été annulée avec succès.']);
    }


}
