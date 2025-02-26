<?php

namespace App\Form;

use App\Entity\Lessons;
use App\Entity\Formations;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Lesson title
            ->add('title', TextType::class, [
                'label' => 'Titre de la leçon',
                'attr' => ['class' => 'form-control']
            ])
            // Lesson content
            ->add('content', TextType::class, [
                'label' => 'Contenu',
                'attr' => ['class' => 'form-control']
            ])
            // URL of the video
            ->add('video_url', UrlType::class, [
                'label' => 'URL de la vidéo',
                'attr' => ['class' => 'form-control']
            ])
            
            // Select the formation to which this lesson belongs
            ->add('formation', EntityType::class, [
                'class' => Formations::class,
                'choice_label' => 'title',  // Displays the formation title in the select
                'label' => 'Formation associée',
                'attr' => ['class' => 'form-control']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter la leçon',
                'attr' => ['class' => 'retour']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lessons::class,
        ]);
    }
}
