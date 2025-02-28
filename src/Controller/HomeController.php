<?php

namespace App\Controller;

use App\Repository\ThemeRepository; 
use App\Repository\FormationsRepository; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
        $theme = $themeRepository->find($id);

        if (!$theme) {
            throw $this->createNotFoundException('Le thème demandé n\'existe pas.');
        }

        return $this->render('home/showFormations.html.twig', [
            'theme' => $theme,
            'formations' => $theme->getFormations(), 
        ]);
    }

    #[Route('/formation/{id}', name: 'app_formations')]
    public function showFormation(int $id, FormationsRepository $FormationsRepository): Response
    {
        $formation = $FormationsRepository->find($id);
        if (!$formation) {
            throw $this->createNotFoundException('Le cursus demandé n\'existe pas.');
        }

        return $this->render('home/showLessons.html.twig', [
            'formation' => $formation,
            'lessons' => $formation->getLessons(), 
        ]);
    }









    // #[Route('/create-checkout-session', name: 'checkout', methods: ['POST'])]
    // public function checkout(SessionInterface $session, StripeService $stripeService): JsonResponse
    // {
    //     $cart = $session->get('cart', []);
    //     $lineItems = [];

    //     foreach ($cart as $id => $quantity) {
    //         $product = $this->productRepository->find($id);
    //         if ($product) {
    //             $lineItems[] = [
    //                 'price_data' => [
    //                     'currency' => 'eur',
    //                     'product_data' => ['name' => $product->getName()],
    //                     'unit_amount' => $product->getPrice() * 100,
    //                 ],
    //                 'quantity' => $quantity,
    //             ];
    //         }
    //     }

    //     $stripeSession = $stripeService->createCheckoutSession(
    //         $lineItems,
    //         $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
    //         $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL)
    //     );

    //     return new JsonResponse(['id' => $stripeSession->id]);
    // }


    // #[Route('/payment-success', name: 'payment_success')]
    // public function paymentSuccess(): Response
    // {
    //     $this->addFlash('success', 'Votre paiement a été validé avec succès ! 🎉');
    //     return $this->redirectToRoute('app_home'); // Redirection vers la page d'accueil
    // }

    // #[Route('/payment-cancel', name: 'payment_cancel')]
    // public function paymentCancel(): Response
    // {
    //     $this->addFlash('error', 'Votre paiement a été annulé. ❌');
    //     return $this->redirectToRoute('app_home'); // Redirection vers la page d'accueil
    // }
}
