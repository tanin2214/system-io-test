<?php

declare(strict_types=1);

namespace App\Enum;

enum CouponTypeEnum: string
{
    case FIX = 'fix';
    case PERCENT = 'percent';
}
