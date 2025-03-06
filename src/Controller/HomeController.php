<?php

namespace App\Controller;

use App\Repository\ThemeRepository;
use App\Repository\FormationsRepository;
use App\Repository\LessonsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Progress;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;


final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ThemeRepository $themeRepository): Response
    {
        $themes = $themeRepository->findAll();

        return $this->render('home/index.html.twig', [
            'themes' => $themes,
        ]);
    }

    #[Route('/theme/{id}', name: 'app_theme')]
    public function showTheme(int $id, ThemeRepository $themeRepository): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        $theme = $themeRepository->find($id);

        if (!$theme) {
            throw $this->createNotFoundException('Le thème demandé n\'existe pas.');
        }

        // Récupérer la clé publique Stripe
        $stripePublicKey = $this->getParameter('stripe_public_key');

        // Tableau pour savoir si l'utilisateur a accès à chaque formation
        $formationsAccess = [];

        if ($user) {
            foreach ($theme->getFormations() as $formation) {
                // Vérifier si l'utilisateur a acheté cette formation
                $formationsAccess[$formation->getId()] = $user->getPurchasedFormations()->contains($formation);
            }
        }

        return $this->render('home/showFormations.html.twig', [
            'theme' => $theme,
            'formations' => $theme->getFormations(),
            'stripe_public_key' => $stripePublicKey,
            'formationsAccess' => $formationsAccess, // Passer les accès pour chaque formation
        ]);
    }


    



    #[Route('/formation/{id}', name: 'app_formations')]
    public function showFormation(int $id, FormationsRepository $formationsRepository): Response
    {
        $formation = $formationsRepository->find($id);
        $stripePublicKey = $this->getParameter('stripe_public_key');

        if (!$formation) {
            throw $this->createNotFoundException('La formation demandée n\'existe pas.');
        }

        /** @var User|null $user */
        $user = $this->getUser();

        // Vérifie si l'utilisateur a acheté la formation
        $hasPurchasedFormation = $user && $user->getPurchasedFormations()->contains($formation);

        // Vérifie l'accès à chaque leçon individuellement
        $lessonsAccess = [];
        foreach ($formation->getLessons() as $lesson) {
            $lessonsAccess[$lesson->getId()] = $hasPurchasedFormation || ($user && $user->getPurchasedLessons()->contains($lesson));
        }

        return $this->render('home/showLessons.html.twig', [
            'formation' => $formation,
            'lessons' => $formation->getLessons(),
            'hasPurchasedFormation' => $hasPurchasedFormation,
            'lessonsAccess' => $lessonsAccess,
            'stripe_public_key' => $stripePublicKey,
        ]);
    }




    #[Route('/lesson/{id}', name: 'lesson_show')]
    public function showLesson(int $id, LessonsRepository $lessonsRepository, EntityManagerInterface $em): Response
    {
        $lesson = $lessonsRepository->find($id);

        if (!$lesson) {
            throw $this->createNotFoundException('La leçon demandée n\'existe pas.');
        }

        /** @var User $user */
        $user = $this->getUser();
        $lessonCompleted = false;

        if ($user) {
            // Vérifier si l'utilisateur a déjà validé cette leçon
            $progress = $em->getRepository(Progress::class)->findOneBy([
                'user' => $user,
                'lesson' => $lesson,
            ]);
            
            if ($progress && $progress->isComplete()) {
                $lessonCompleted = true; // Si la leçon est validée, on met la variable à true
            }
        }

        return $this->render('home/lesson.html.twig', [
            'lesson' => $lesson,
            'lessonCompleted' => $lessonCompleted,  // Passer la variable au template
        ]);
    }


    

    

    #[Route('/lesson/validate/{id}', name: 'lesson_validate', methods: ['POST'])]
    public function validateLesson(int $id, LessonsRepository $lessonsRepository, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour valider une leçon.');
        }

        $lesson = $lessonsRepository->find($id);

        if (!$lesson) {
            throw $this->createNotFoundException('Leçon introuvable.');
        }

        // Vérifier si la leçon est déjà validée
        $progress = $em->getRepository(Progress::class)->findOneBy([
            'user' => $user,
            'lesson' => $lesson
        ]);

        if (!$progress) {
            // Si l'utilisateur n'a pas encore validé cette leçon, créer un nouvel enregistrement
            $progress = new Progress();
            $progress->setUser($user);
            $progress->setLesson($lesson);
            $progress->setIsComplete(true);  // Marquer la leçon comme terminée
            $em->persist($progress);
            $em->flush();
        } else {
            // Si la leçon est déjà validée, ne pas effectuer de modification
            if ($progress->isComplete()) {
                return $this->redirectToRoute('lesson_show', ['id' => $id]);
            }
            // Sinon, on marque la leçon comme terminée
            $progress->setIsComplete(true);
            $em->persist($progress);
            $em->flush();
        }

        // Vérifier si toutes les leçons de la formation sont complétées
        $formation = $lesson->getFormation();
        $allLessons = $formation->getLessons();

        // Vérifie si chaque leçon a été validée
        $completedLessonsCount = 0;
        foreach ($allLessons as $lessonItem) {
            $progress = $em->getRepository(Progress::class)->findOneBy([
                'user' => $user,
                'lesson' => $lessonItem
            ]);

            if ($progress && $progress->isComplete()) {  
                $completedLessonsCount++;
            }
        }

        if ($completedLessonsCount === count($allLessons)) {
            // Vérifie si un enregistrement Progress existe pour cette formation
            $progressFormation = $em->getRepository(Progress::class)->findOneBy([
                'user' => $user,
                'formation' => $formation
            ]);

            if (!$progressFormation) {
                $progressFormation = new Progress();
                $progressFormation->setUser($user);
                $progressFormation->setFormation($formation);
            }

            $progressFormation->setIsComplete(true);
            $em->persist($progressFormation);
            $em->flush();
        }

        return $this->redirectToRoute('lesson_show', ['id' => $id]);
    }
}