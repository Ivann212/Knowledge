<?php

namespace App\Controller;

use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\FormationsRepository;
use App\Repository\LessonsRepository;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class PurchaseController extends AbstractController
{
    /**
     * Creates a Stripe checkout session for a formation or a lesson.
     * 
     * @param string $type The type of the item ('formation' or 'lesson').
     * @param int $id The ID of the item.
     * @param FormationsRepository $formationsRepository Repository for formations.
     * @param LessonsRepository $lessonsRepository Repository for lessons.
     * @param StripeService $stripeService Service to handle Stripe payments.
     * 
     * @return JsonResponse Returns a JSON response containing the session ID or an error message.
     */
    #[Route('/paiement/create-session/{type}/{id}', name: 'create_checkout_session', methods: ['POST'])]
    public function createSession(
        string $type, 
        int $id, 
        FormationsRepository $formationsRepository, 
        LessonsRepository $lessonsRepository, 
        StripeService $stripeService
    ): JsonResponse {
        // Retrieve the item based on type
        if ($type === 'formation') {
            $item = $formationsRepository->find($id);
        } elseif ($type === 'lesson') {
            $item = $lessonsRepository->find($id);
        } else {
            return new JsonResponse(['error' => 'Invalid product type'], Response::HTTP_BAD_REQUEST);
        }

        // Check if the item exists
        if (!$item) {
            return new JsonResponse(['error' => ucfirst($type) . ' not found'], Response::HTTP_NOT_FOUND);
        }

        // Define the Stripe checkout session line items
        $lineItems = [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $item->getTitle(),
                ],
                'unit_amount' => $item->getPrice() * 100, // Convert price to cents
            ],
            'quantity' => 1,
        ]];

        // Create the Stripe checkout session
        $session = $stripeService->createCheckoutSession(
            $lineItems,
            'http://127.0.0.1:8000/success/' . $type . '/' . $id,
            'http://127.0.0.1:8000/cancel'
        );

        return new JsonResponse(['id' => $session->id]);
    }

    /**
     * Handles successful payment and grants user access to the purchased content.
     * 
     * @param string $type The type of the item ('formation' or 'lesson').
     * @param int $id The ID of the item.
     * @param EntityManagerInterface $em Entity manager to persist user purchases.
     * @param FormationsRepository $formationsRepository Repository for formations.
     * @param LessonsRepository $lessonsRepository Repository for lessons.
     * 
     * @return Response Renders the success page after payment.
     */
    #[Route('/success/{type}/{id}', name: 'payment_success')]
    public function paymentSuccess(
        string $type, 
        int $id, 
        EntityManagerInterface $em, 
        FormationsRepository $formationsRepository, 
        LessonsRepository $lessonsRepository
    ): Response {
        /** @var User $user */
        $user = $this->getUser(); // Get the currently logged-in user

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in.');
        }

        // Retrieve the purchased item and assign it to the user
        if ($type === 'formation') {
            $item = $formationsRepository->find($id);
            $user->addPurchasedFormation($item); 
        } elseif ($type === 'lesson') {
            $item = $lessonsRepository->find($id);
            $user->addPurchasedLesson($item); 
        } else {
            throw $this->createNotFoundException('Invalid type.');
        }

        // Persist the user's purchase in the database
        $em->persist($user);
        $em->flush();

        // Render the success page
        return $this->render('home/success.html.twig', [
            'item' => $item,
            'type' => $type
        ]);
    }
}
