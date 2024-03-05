<?php

namespace App\Form;

use App\Entity\Rendezvous;
use App\Entity\User;
use App\Entity\Medecin;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository; // Ajoutez cette ligne

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    private $entityManager;
    private $reservationRepository; // Ajoutez cette ligne

    public function __construct(EntityManagerInterface $entityManager, ReservationRepository $reservationRepository)
    {
        $this->entityManager = $entityManager;
        $this->reservationRepository = $reservationRepository; // Ajoutez cette ligne
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Utilisez les options pour récupérer les rendez-vous disponibles
        $availableRendezvous = $options['available_rendezvous'];

        $builder
            ->add('description')
            ->add('status')
            ->add('patient', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getNom() . ' ' . $user->getPrenom();
                },
            ])
            ->add('medecin', EntityType::class, [
                'class' => Medecin::class,
                'choice_label' => function (Medecin $medecin) {
                    return $medecin->getNom() . ' ' . $medecin->getPrenom();
                },
            ])
            ->add('rendezvous', EntityType::class, [
                'class' => Rendezvous::class,
                'choice_label' => function (Rendezvous $rendezvous) {
                    return $rendezvous->getDate()->format('Y-m-d H:i:s');
                },
                //'choices' => $this->reservationRepository->findAvailableDates(), // Utilisez la propriété repository
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'entityManager' => null,
            'available_rendezvous' => [], // Ajoutez cette ligne pour définir les options
        ]);
    }
}
