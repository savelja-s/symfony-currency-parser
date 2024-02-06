<?php


declare(strict_types=1);

namespace App\Interface\Service;

use App\Dto\ExchangeRateWithThresholdChange;
use App\Entity\ExchangeRate;
use App\Enum\CurrencyEnum;

interface BankParserServiceInterface
{
    /**
     * @param  array<CurrencyEnum,ExchangeRate>  $currentValues
     * @return ExchangeRateWithThresholdChange[]
     */
    public function trackingChange(array $currentValues): array;
}