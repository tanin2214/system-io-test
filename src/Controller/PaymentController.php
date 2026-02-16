<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Request\CalculatePriceRequest;
use App\Dto\Request\PurchaseRequest;
use App\Dto\Response\CalculatePriceResponse;
use App\Services\CalculatePriceService;
use App\Services\PurchaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

class PaymentController extends AbstractController
{
    #[Route('/calculate-price', name: 'calculate_price', methods: ['POST'])]
    public function calculatePrice(
        #[ValueResolver('calculatePriceRequestResolver')]
        CalculatePriceRequest $request,
        CalculatePriceService $service,
    ): Response {
        $calculatedPrice = $service->calculatePrice(
            productId: $request->product,
            taxNumber: $request->taxNumber,
            couponCode: $request->couponCode,
        );

        return $this->json(data: new CalculatePriceResponse(calculatedPrice: $calculatedPrice));
    }

    #[Route('/purchase', name: 'purchase', methods: ['POST'])]
    public function purchase(
        #[ValueResolver('purchaseRequestResolver')]
        PurchaseRequest $request,
        PurchaseService $purchaseService,
    ): Response {
        $result = $purchaseService->purchase(
            productId: $request->product,
            taxNumber: $request->taxNumber,
            couponCode: $request->couponCode,
            paymentProcessorType: $request->paymentProcessor,
        );

        return $this->json(data: [
            'status' => $result,
        ]);
    }
}
