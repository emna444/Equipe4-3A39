<?php

namespace App\Form;

use App\Entity\Rendezvous;
use App\Entity\User;
use App\Entity\Medecin;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository; // Ajoutez cette ligne
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Security;

class ReservationType extends AbstractType
{
    private $entityManager;
    private $reservationRepository; // Ajoutez cette ligne
    private $security;

 
    public function __construct(EntityManagerInterface $entityManager,Security $security, ReservationRepository $reservationRepository)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->reservationRepository = $reservationRepository; // Ajoutez cette ligne
    }
    

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupérer l'utilisateur connecté depuis les options
        $user = $options['user'];
    
        $builder
            ->add('description')
            ->add('status')
            ->add('patient', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getNom() . ' ' . $user->getPrenom();
                },
                'disabled' => true,
                'data' => $user, // Remplir le champ avec l'utilisateur connecté
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
            ]);
    }
    
    
    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'user' => null, // Définissez l'option "user" par défaut à null
            'available_rendezvous' => [], // Vous avez déjà cette option définie
        ]);

        // Assurez-vous que l'option "user" est de type UserInterface ou null
        $resolver->setAllowedTypes('user', [UserInterface::class, 'null']);
    }
}
