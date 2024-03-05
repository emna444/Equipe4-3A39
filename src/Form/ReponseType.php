<?php

namespace App\Form;

use App\Entity\Reponse;
use App\Entity\Reclamation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'constraints' => [
                    new NotBlank(['message' => 'La date est requise.']),
                ],
            ])
            ->add('contenu', TextType::class, [
                'label' => 'Contenu',
                'constraints' => [
                    new NotBlank(['message' => 'Le contenu est requis.']),
                ],
            ])
            ->add('reclamation', EntityType::class, [
                'class' => Reclamation::class,
                'choice_label' => 'description', // Supposant que la description est l'attribut à afficher pour les réclamations
                'placeholder' => 'Sélectionner une réclamation',
                'label' => 'Réclamation',
                'constraints' => [
                    new NotBlank(['message' => 'La réclamation est requise.']),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reponse::class,
        ]);
    }
}
