<?php

declare(strict_types=1);

namespace App\Service\parser;

use App\Dto\ExchangeRateChange;
use App\Enum\BankEnum;
use App\Enum\CurrencyEnum;
use App\Enum\CurrencyISO4217Enum;
use App\Interface\Service\BankParserServiceInterface;

class MonobankParserService extends AbstractBankParserService implements BankParserServiceInterface
{
    /**
     * @param  array<integer,array<string,integer>>  $data
     * @param  CurrencyEnum[]  $currencies
     * @return array|ExchangeRateChange[]
     */
    protected function prepareExchangeRate(array $data, array $currencies): array
    {
        $currencyValues = [];
        foreach ($currencies as $currency) {
            $isoVal = CurrencyISO4217Enum::fromName($currency->name);
            $currencyValues[] = $isoVal?->value ?? 'none';
        }
        $currencyUAH = CurrencyISO4217Enum::UAH->value;
        $result = null;
        foreach ($data as $currencyItem) {
            $currencyFromValue = $currencyItem['currencyCodeA'] ?? null;
            $currencyTo = $currencyItem['currencyCodeB'] ?? null;
            $buy = $currencyItem['rateBuy'] ?? null;
            $sale = $currencyItem['rateSell'] ?? null;
            $isFound = in_array($currencyFromValue, $currencyValues) && $currencyTo == $currencyUAH;
            if (!$isFound || !$buy || !$sale) {
                continue;
            }
            $currencyISO = CurrencyISO4217Enum::tryFrom((string) $currencyFromValue);
            $currency = CurrencyEnum::tryFrom($currencyISO->name);
            $result[] = $this->createExchangeRateChange($currency, $buy, $sale);
        }
        return $result;
    }

    protected function getBank(): BankEnum
    {
        return BankEnum::MONOBANK;
    }
}