<?php


declare(strict_types=1);

namespace App\Enum;


enum ExchangeRateStatusEnum: string
{
    case Current = 'CURRENT';
    case Old = 'Old';
}
