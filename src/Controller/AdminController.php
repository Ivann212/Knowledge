<?php

namespace App\Controller;

use App\Entity\Formations;
use App\Entity\Lessons;
use App\Form\FormationsType;
use App\Form\LessonsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    // Page pour ajouter un cursus (Formation)
    #[Route('/admin/formations/add', name: 'admin_formations_add')]
    public function addFormation(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formation = new Formations();
        $form = $this->createForm(FormationsType::class, $formation);
    
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // La relation avec Theme est gérée automatiquement via le formulaire
            $entityManager->persist($formation);
            $entityManager->flush();
    
            $this->addFlash('success', 'Formation ajoutée avec succès !');
    
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/formations/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Page pour ajouter une leçon
    #[Route('/admin/lessons/add', name: 'admin_lessons_add')]
    public function addLesson(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lesson = new Lessons();
        $form = $this->createForm(LessonsType::class, $lesson);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lesson);
            $entityManager->flush();

            $this->addFlash('success', 'Leçon ajoutée avec succès !');

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/lessons/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Page pour gérer les utilisateurs
    #[Route('/admin/users', name: 'admin_users')]
    public function manageUsers(): Response
    {
        // Logique pour gérer les utilisateurs
        return $this->render('admin/users/manage.html.twig');
    }


}
