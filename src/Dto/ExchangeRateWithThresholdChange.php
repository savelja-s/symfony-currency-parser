<?php


declare(strict_types=1);

namespace App\Dto;

use App\Enum\CurrencyEnum;

class ExchangeRateWithThresholdChange extends ExchangeRateChange
{
    public function __construct(
        public CurrencyEnum $currency,
        public int $buy,
        public int $sale,
        public int $buyThreshold,
        public int $saleThreshold
    ) {
        parent::__construct($currency, $buy, $sale);
    }

    public function getNewBuy(): int
    {
        return $this->buyThreshold + $this->buy;
    }

    public function getNewSale(): int
    {
        return $this->saleThreshold + $this->sale;
    }
}