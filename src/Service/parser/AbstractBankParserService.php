<?php


declare(strict_types=1);

namespace App\Service\parser;

use App\Dto\ExchangeRateChange;
use App\Dto\ExchangeRateWithThresholdChange;
use App\Entity\ExchangeRate;
use App\Enum\BankEnum;
use App\Enum\CurrencyEnum;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractBankParserService
{
    public function __construct(
        protected readonly HttpClientInterface $httpClient,
        protected ParameterBagInterface $parameterBag
    ) {
    }

    protected function preparePrice(string|int|float $value): int
    {
        return (int) (round((float) $value, 2) * 100);
    }

    protected function createExchangeRateChange(
        CurrencyEnum $currency,
        string|int|float $buy,
        string|int|float $sale
    ): ExchangeRateChange {
        return new ExchangeRateChange($currency, $this->preparePrice($buy), $this->preparePrice($sale));
    }

    /**
     * @param  array  $data
     * @param  CurrencyEnum[]  $currencies
     * @return ExchangeRateChange[]
     */
    protected abstract function prepareExchangeRate(array $data, array $currencies): array;

    protected abstract function getBank(): BankEnum;

    protected function getBankApiUrl(): string
    {
        return $this->parameterBag->get($this->getBank()->name.'_API_URL');
    }

    /**
     * @param  array<CurrencyEnum,ExchangeRate>  $currentValues
     * @return ExchangeRateWithThresholdChange[]
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function trackingChange(array $currentValues): array
    {
        $currencies = array_map(static fn(ExchangeRate $er) => $er->getCurrency(), $currentValues);
        $bankExchangeRates = $this->getBankExchangeRate($currencies);
        $result = [];
        foreach ($bankExchangeRates as $bankExchangeRate) {
            $onDBExchangeRate = $currentValues[$bankExchangeRate->currency->name];
            $buyDiff = $bankExchangeRate->buy - $onDBExchangeRate->getBuy();
            $saleDiff = $bankExchangeRate->sale - $onDBExchangeRate->getSale();
            $result[] = new ExchangeRateWithThresholdChange(
                $bankExchangeRate->currency,
                $bankExchangeRate->buy,
                $bankExchangeRate->sale,
                $buyDiff,
                $saleDiff
            );
        }
        return $result;
    }

    /**
     * @param  CurrencyEnum[]  $currencies
     * @return ExchangeRateChange[]
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function getBankExchangeRate(array $currencies): array
    {
        $response = $this->httpClient->request(Request::METHOD_GET, $this->getBankApiUrl());
        return $this->prepareExchangeRate($response->toArray(), $currencies);
    }
}