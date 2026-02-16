<?php

declare(strict_types=1);

namespace App\Services\PaymentProcessors;

use App\Enum\PaymentProcessorTypeEnum;
use App\Exception\AppException;
use Symfony\Component\HttpFoundation\Response;

class PaymentProcessorProvider
{
    /**
     * @param PaymentProcessorInterface[]|iterable $paymentProcessors
     */
    public function __construct(
        private iterable $paymentProcessors,
    ) {
    }

    public function getPaymentProcessorByType(PaymentProcessorTypeEnum $paymentProcessorType): PaymentProcessorInterface
    {
        foreach ($this->paymentProcessors as $paymentProcessor) {
            if ($paymentProcessor->support($paymentProcessorType)) {
                return $paymentProcessor;
            }
        }

        throw new AppException(
            userInterfaceMessage: 'Ошибка проведения платежа',
            httpStatusCode: Response::HTTP_BAD_REQUEST,
        );
    }
}
