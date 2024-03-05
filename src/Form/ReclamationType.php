<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'attr' => ['class' => 'datepicker'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La date est requise.'
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La description est requise.'
                    ]),
                    new Assert\Length([
                        'min' => 20,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Negative' => 'Negative',
                    'Positive' => 'Positive',
                    'Neutre' => 'Neutre',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le type est requis.'
                    ]),
                ],
            ])
            ->add('priorite', ChoiceType::class, [
                'label' => 'Priorité',
                'choices' => [
                    'Haute' => 'Haute',
                    'Moyenne' => 'Moyenne',
                    'Basse' => 'Basse',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La priorité est requise.'
                    ]),
                ],
            ])
            ->add('commentaires', TextareaType::class, [
                'label' => 'Commentaires',
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Les commentaires sont requis.'
                    ]),
                    new Assert\Length([
                        'min' => 20,
                        'minMessage' => 'Les commentaires doivent contenir au moins {{ limit }} caractères.'
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
