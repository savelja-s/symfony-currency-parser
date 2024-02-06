<?php


declare(strict_types=1);

namespace App\Dto;

use App\Enum\CurrencyEnum;

class ExchangeRateChange
{
    public function __construct(public CurrencyEnum $currency, public int $buy, public int $sale)
    {
    }
}