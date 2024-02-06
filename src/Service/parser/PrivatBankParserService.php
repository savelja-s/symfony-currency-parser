<?php


declare(strict_types=1);

namespace App\Service\parser;

use App\Dto\ExchangeRateChange;
use App\Enum\BankEnum;
use App\Enum\CurrencyEnum;
use App\Interface\Service\BankParserServiceInterface;

class PrivatBankParserService extends AbstractBankParserService implements BankParserServiceInterface
{
    protected function prepareExchangeRate(array $data, array $currencies): array
    {
        $result = null;
        $currencyUAH = CurrencyEnum::UAH->value;
        $currencyValues = array_column($currencies, 'value');
        foreach ($data as $item) {
            $fromC = $item['ccy'] ?? null;
            $toC = $item['base_ccy'] ?? null;
            $buy = $item['buy'] ?? null;
            $sale = $item['sale'] ?? null;
            if ($currencyUAH !== $toC || !in_array($fromC, $currencyValues) || !$buy || !$sale) {
                continue;
            }
            $currency = CurrencyEnum::tryFrom($fromC);
            $result[] = $this->createExchangeRateChange($currency, $buy, $sale);
        }
        return $result;
    }

    protected function getBank(): BankEnum
    {
        return BankEnum::PRIVAT_BANK;
    }
}