<?php

namespace App\Form;

use App\Entity\Suivi;

use App\Entity\Rendezvous;
use App\Entity\user;
use App\Entity\Medecin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SuiviType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ordonnance')
            ->add('user', EntityType::class,[
                'class'=> User::class,
                'choice_label' => function (User $user) {
                    return $user->getNom() . ' ' . $user->getPrenom();}
            ])
            ->add('medecin', EntityType::class,[
                'class'=> Medecin::class,
                'choice_label' => function (Medecin $medecin) {
                    return $medecin->getNom() . ' ' . $medecin->getPrenom();}
            ])
            ->add('rendezvous', EntityType::class, [
                'class' => Rendezvous::class,
                'choice_label' => function (Rendezvous $rendezvous) {
                    return $rendezvous->getDate()->format('Y-m-d H:i:s');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Suivi::class,
        ]);
    }
}
