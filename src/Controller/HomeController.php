<?php

namespace App\Controller;

use App\Repository\ThemeRepository; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ThemeRepository $themeRepository): Response
    {
        // Récupère tous les thèmes
        $themes = $themeRepository->findAll();

        return $this->render('home/index.html.twig', [
            'themes' => $themes,  // Passe les thèmes au template
        ]);
    }

    #[Route('/theme/{id}', name: 'app_theme')]
    public function show(int $id, ThemeRepository $themeRepository): Response
    {
        $theme = $themeRepository->find($id);

        if (!$theme) {
            throw $this->createNotFoundException('Le thème demandé n\'existe pas.');
        }

        return $this->render('theme/show.html.twig', [
            'theme' => $theme,
        ]);
    }

}
