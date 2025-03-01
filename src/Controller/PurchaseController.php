<?php


namespace App\Controller;

use App\Service\StripeService;
use App\Entity\Purchases;
use App\Entity\User;
use App\Entity\Formations;
use App\Entity\Lessons;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PurchaseController extends AbstractController
{
    private StripeService $stripeService;
    private EntityManagerInterface $entityManager;

    public function __construct(StripeService $stripeService, EntityManagerInterface $entityManager)
    {
        $this->stripeService = $stripeService;
        $this->entityManager = $entityManager;
    }

    #[Route('/purchase/{type}/{id}', name: 'purchase')]
    public function purchase(string $type, int $id, Request $request)
    {
        $user = $this->getUser(); // Assurez-vous que l'utilisateur est authentifié

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($type === 'formation') {
            $formation = $this->entityManager->getRepository(Formations::class)->find($id);
            if (!$formation) {
                throw $this->createNotFoundException('Formation non trouvée');
            }

            // Créez une session de paiement pour la formation entière
            $lineItems = [];
            foreach ($formation->getLessons() as $lesson) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $lesson->getTitle(),
                        ],
                        'unit_amount' => $lesson->getPrice() * 100, 
                    ],
                    'quantity' => 1,
                ];
            }

            // Créez la session Stripe
            $checkoutSession = $this->stripeService->createCheckoutSession(
                $lineItems,
                $this->generateUrl('purchase_success', [], 0), 
                $this->generateUrl('purchase_cancel', [], 0) 
            );

            return $this->redirect($checkoutSession->url);
        }

        if ($type === 'lesson') {
            $lesson = $this->entityManager->getRepository(Lessons::class)->find($id);
            if (!$lesson) {
                throw $this->createNotFoundException('Leçon non trouvée');
            }

            // Créez une session de paiement pour une seule leçon
            $lineItems = [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $lesson->getTitle(),
                        ],
                        'unit_amount' => $lesson->getPrice() * 100, 
                    ],
                    'quantity' => 1,
                ]
            ];


            $checkoutSession = $this->stripeService->createCheckoutSession(
                $lineItems,
                $this->generateUrl('purchase_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->generateUrl('purchase_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL)
            );


            return $this->redirect($checkoutSession->url);
        }

        // Gérer le cas où le type n'est ni une formation ni une leçon
        throw $this->createNotFoundException('Type d\'achat non valide');
    }

    #[Route('/purchase/success', name: 'purchase_success')]
    public function success(Request $request)
    {
        // Logique pour associer l'achat à l'utilisateur après paiement réussi
        // Rediriger l'utilisateur vers la page de succès ou la liste des achats
        $this->addFlash('success', 'Votre paiement a été validé avec succès ! 🎉');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/purchase/cancel', name: 'purchase_cancel')]
    public function cancel()
    {
        $this->addFlash('error', 'Votre paiement a été annulé. ❌');
        return $this->redirectToRoute('app_home');
    }

    public function recordPurchase($user, $price , $formation = null, $lesson = null)
    {
        $purchase = new Purchases();
        $purchase->setUser($user);
        $purchase->setPrice($price);

        if ($formation) {
            $purchase->setFormation($formation);
            // Associez toutes les leçons de la formation à l'achat
            foreach ($formation->getLessons() as $lesson) {
                // Logique pour associer la leçon à l'achat
            }
        }

        if ($lesson) {
            $purchase->setLesson($lesson);
        }

        $this->entityManager->persist($purchase);
        $this->entityManager->flush();
    }

}
