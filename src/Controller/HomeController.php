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
    /**
     * Displays the homepage with all available themes.
     * 
     * @param ThemeRepository $themeRepository The repository to fetch themes from the database.
     * @return Response The rendered homepage with themes.
     */
    #[Route('/', name: 'app_home')]
    public function index(ThemeRepository $themeRepository): Response
    {
        $themes = $themeRepository->findAll();

        return $this->render('home/index.html.twig', [
            'themes' => $themes,
        ]);
    }

    /**
     * Displays a theme's details including its formations and access control for the current user.
     * 
     * @param int $id The ID of the theme to display.
     * @param ThemeRepository $themeRepository The repository to fetch theme data.
     * @return Response The rendered theme page with formations and access control.
     */
    #[Route('/theme/{id}', name: 'app_theme')]
    public function showTheme(int $id, ThemeRepository $themeRepository): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        $theme = $themeRepository->find($id);

        if (!$theme) {
            throw $this->createNotFoundException('The requested theme does not exist.');
        }

        // Retrieve the Stripe public key
        $stripePublicKey = $this->getParameter('stripe_public_key');

        // Array to track whether the user has access to each formation
        $formationsAccess = [];

        if ($user) {
            foreach ($theme->getFormations() as $formation) {
                // Check if the user has purchased this formation
                $formationsAccess[$formation->getId()] = $user->getPurchasedFormations()->contains($formation);
            }
        }

        return $this->render('home/showFormations.html.twig', [
            'theme' => $theme,
            'formations' => $theme->getFormations(),
            'stripe_public_key' => $stripePublicKey,
            'formationsAccess' => $formationsAccess, // Pass user access information for each formation
        ]);
    }

    /**
     * Displays a formation's details including its lessons and user access.
     * 
     * @param int $id The ID of the formation to display.
     * @param FormationsRepository $formationsRepository The repository to fetch formation data.
     * @return Response The rendered formation page with lessons and access control.
     */
    #[Route('/formation/{id}', name: 'app_formations')]
    public function showFormation(int $id, FormationsRepository $formationsRepository): Response
    {
        $formation = $formationsRepository->find($id);
        $stripePublicKey = $this->getParameter('stripe_public_key');

        if (!$formation) {
            throw $this->createNotFoundException('The requested formation does not exist.');
        }

        /** @var User|null $user */
        $user = $this->getUser();

        // Check if the user has purchased the formation
        $hasPurchasedFormation = $user && $user->getPurchasedFormations()->contains($formation);

        // Check access to each lesson
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

    /**
     * Displays a lesson's details and completion status.
     * 
     * @param int $id The ID of the lesson to display.
     * @param LessonsRepository $lessonsRepository The repository to fetch lesson data.
     * @param EntityManagerInterface $em The entity manager to query the progress.
     * @return Response The rendered lesson page with completion status.
     */
    #[Route('/lesson/{id}', name: 'lesson_show')]
    public function showLesson(int $id, LessonsRepository $lessonsRepository, EntityManagerInterface $em): Response
    {
        $lesson = $lessonsRepository->find($id);

        if (!$lesson) {
            throw $this->createNotFoundException('The requested lesson does not exist.');
        }

        /** @var User $user */
        $user = $this->getUser();
        $lessonCompleted = false;

        if ($user) {
            // Check if the user has already completed the lesson
            $progress = $em->getRepository(Progress::class)->findOneBy([
                'user' => $user,
                'lesson' => $lesson,
            ]);
            
            if ($progress && $progress->isComplete()) {
                $lessonCompleted = true; // Set lesson completion to true if already marked complete
            }
        }

        return $this->render('home/lesson.html.twig', [
            'lesson' => $lesson,
            'lessonCompleted' => $lessonCompleted,  // Pass completion status to the template
        ]);
    }

    /**
     * Marks a lesson as completed for the logged-in user.
     * 
     * @param int $id The ID of the lesson to validate.
     * @param LessonsRepository $lessonsRepository The repository to fetch lesson data.
     * @param EntityManagerInterface $em The entity manager to update the progress.
     * @return Response A redirect to the lesson page after validation.
     */
    #[Route('/lesson/validate/{id}', name: 'lesson_validate', methods: ['POST'])]
    public function validateLesson(int $id, LessonsRepository $lessonsRepository, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to validate a lesson.');
        }

        $lesson = $lessonsRepository->find($id);

        if (!$lesson) {
            throw $this->createNotFoundException('Lesson not found.');
        }

        // Check if the lesson has already been validated
        $progress = $em->getRepository(Progress::class)->findOneBy([
            'user' => $user,
            'lesson' => $lesson
        ]);

        if (!$progress) {
            // Create a new progress record if the lesson has not been validated yet
            $progress = new Progress();
            $progress->setUser($user);
            $progress->setLesson($lesson);
            $progress->setIsComplete(true);  // Mark the lesson as complete
            $em->persist($progress);
            $em->flush();
        } else {
            // If the lesson is already validated, skip modification
            if ($progress->isComplete()) {
                return $this->redirectToRoute('lesson_show', ['id' => $id]);
            }
            // Otherwise, mark the lesson as complete
            $progress->setIsComplete(true);
            $em->persist($progress);
            $em->flush();
        }

        // Check if all lessons in the formation are completed
        $formation = $lesson->getFormation();
        $allLessons = $formation->getLessons();

        // Count the number of completed lessons
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

        // If all lessons are completed, mark the formation as complete
        if ($completedLessonsCount === count($allLessons)) {
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
