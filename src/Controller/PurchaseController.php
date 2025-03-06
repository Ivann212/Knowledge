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
    #[Route('/paiement/create-session/{type}/{id}', name: 'create_checkout_session', methods: ['POST'])]
    public function createSession(
        string $type, 
        int $id, 
        FormationsRepository $formationsRepository, 
        LessonsRepository $lessonsRepository, 
        StripeService $stripeService
    ): JsonResponse {
        if ($type === 'formation') {
            $item = $formationsRepository->find($id);
        } elseif ($type === 'lesson') {
            $item = $lessonsRepository->find($id);
        } else {
            return new JsonResponse(['error' => 'Type de produit invalide'], Response::HTTP_BAD_REQUEST);
        }

        if (!$item) {
            return new JsonResponse(['error' => ucfirst($type) . ' non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $lineItems = [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $item->getTitle(),
                ],
                'unit_amount' => $item->getPrice() * 100, 
            ],
            'quantity' => 1,
        ]];

        $session = $stripeService->createCheckoutSession(
            $lineItems,
            'http://127.0.0.1:8000/success/' . $type . '/' . $id,
            'http://127.0.0.1:8000/cancel'
        );

        return new JsonResponse(['id' => $session->id]);
    }


    #[Route('/success/{type}/{id}', name: 'payment_success')]
    public function paymentSuccess(
        string $type, 
        int $id, 
        EntityManagerInterface $em, 
        FormationsRepository $formationsRepository, 
        LessonsRepository $lessonsRepository
    ): Response {
        /** @var User $user */
        $user = $this->getUser(); 

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        if ($type === 'formation') {
            $item = $formationsRepository->find($id);
            $user->addPurchasedFormation($item); 
        } elseif ($type === 'lesson') {
            $item = $lessonsRepository->find($id);
            $user->addPurchasedLesson($item); 
        } else {
            throw $this->createNotFoundException('Type invalide.');
        }

        
        
        $em->persist($user);
        $em->flush();

        return $this->render('home/success.html.twig', [
            'item' => $item,
            'type' => $type
        ]);
    }

}
