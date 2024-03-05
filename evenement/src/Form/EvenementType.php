<?php

namespace App\Form;


use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Evenement;
use App\Entity\Partenaire;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

use Karser\Recaptcha3Bundle\Form\Type\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

use Karser\Recaptcha3Bundle\Form\Recaptcha3Type as FormRecaptcha3Type;

use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Validator\Constraints\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('date_debut', DateTimeType::class, [
                'date_widget' => 'single_text' ])
            ->add('date_fin', DateTimeType::class, [
                'date_widget' => 'single_text'
            ])
            ->add('lieu')
            ->add('description')
            ->add('background_color', ColorType::class)
            ->add('text_color', ColorType::class)
            ->add('photo', FileType::class, [
                'required' => false,
                'mapped' => false,
                'constraints' => [new Image(['maxSize' => '5000k'])]
            ])
            ->add('cats',EntityType::class,[
                'class' => Categories::class,
                'choice_label' => 'name'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
