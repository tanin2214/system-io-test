<?php

declare(strict_types=1);

namespace App\Dto\Request;

use App\Enum\PaymentProcessorTypeEnum;
use Symfony\Component\Validator\Constraints as Assert;

class PurchaseRequest
{
    public function __construct(
        #[Assert\Range(
            notInRangeMessage: 'Значение должно быть между {{ min }} and {{ max }}',
            min: 1,
            max: 100,
        )]
        public readonly int $product,
        #[Assert\Regex(
            pattern: '/^(DE\d{9}|IT\d{11}|GR\d{9}|FR[A-Za-z]{2}\d{9})$/',
            message: 'Неверный формат налогового номера.',
        )]
        public readonly string $taxNumber,
        #[Assert\NotNull(message: 'Не указан платежный провайдер')]
        public readonly PaymentProcessorTypeEnum $paymentProcessor,
        #[Assert\Length(
            min: 3,
            minMessage: 'Значение поля должно быть больше 3 символов',
        )]
        public readonly ?string $couponCode = null,
    ) {
    }
}
