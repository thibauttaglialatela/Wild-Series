<?php

namespace App\Form;

use App\Entity\Program;
use App\Entity\Actor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('synopsis', TextareaType::class, ['label' => 'Résumé'])
            ->add('poster', TextType::class, ['label' => 'Image'])
            ->add('posterFile', VichFileType::class, [
                'required' => false,
            ])
            ->add('country', TextType::class, ['label' => 'Pays'])
            ->add('year', TextType::class, ['label' => 'Année'])
            ->add('category', null, ['choice_label' => 'name', 'label' => 'Catégorie'])
            ->add('actors', EntityType::class, [
                'class' => Actor::class,
                'by_reference' => false,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Acteurs'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
        ]);
    }
}
