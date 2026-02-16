<?php

declare(strict_types=1);

namespace App\Enum;

enum PaymentProcessorTypeEnum: string
{
    case PAYPAL = 'paypal';
    case STRIPE = 'stripe';
}
