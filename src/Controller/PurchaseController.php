<?php

namespace App\Controller;

use App\Entity\Lessons;
use App\Entity\Formations;
use App\Repository\LessonsRepository;
use App\Repository\FormationsRepository;
use App\Service\StripeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;  

class PurchaseController extends AbstractController
{
    private StripeService $stripeService;
    private LessonsRepository $lessonsRepository;
    private FormationsRepository $formationsRepository;

    public function __construct(
        StripeService $stripeService,
        LessonsRepository $lessonsRepository,
        FormationsRepository $formationsRepository
    ) {
        $this->stripeService = $stripeService;
        $this->lessonsRepository = $lessonsRepository;
        $this->formationsRepository = $formationsRepository;
    }

    #[Route('/purchase/{productId}/{type}', name: 'purchase')]
    public function purchase(int $productId, string $type): RedirectResponse
    {
        $user = $this->getUser();

        // Récupérer le produit en fonction du type
        $product = match ($type) {
            'lesson' => $this->lessonsRepository->find($productId),
            'formation' => $this->formationsRepository->find($productId),
            default => throw $this->createNotFoundException('Type de produit invalide.')
        };

        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé.');
        }

        // Créer la session Stripe
        $sessionUrl = $this->stripeService->createCheckoutSession($user->getId(), $product, $type);

        return $this->redirect($sessionUrl);
    }

    #[Route('/success', name: 'purchase_success')]
    public function success(Request $request)
    {
        $sessionId = $request->query->get('session_id');

        if (!$sessionId) {
            throw $this->createNotFoundException('Session Stripe introuvable.');
        }

        $this->stripeService->handleStripeCallback($sessionId);

        return $this->render('purchase/success.html.twig');
    }

    #[Route('/cancel', name: 'purchase_cancel')]
    public function cancel()
    {
        return $this->render('purchase/cancel.html.twig');
    }
}
