<?php


declare(strict_types=1);

namespace App\Service;

use App\Dto\ExchangeRateHistoryDto;
use App\Dto\ExchangeRateWithThresholdChange;
use App\Entity\ExchangeRate;
use App\Entity\ExchangeRateHistory;
use App\Enum\BankEnum;
use App\Enum\CurrencyEnum;
use App\Enum\ExchangeRateStatusEnum;
use App\Exception\ParseExchangeRateException;
use App\Interface\Service\BankParserServiceInterface;
use App\Repository\ExchangeRateRepository;
use App\Service\parser\MonobankParserService;
use App\Service\parser\PrivatBankParserService;
use Doctrine\ORM\Query\QueryException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ParserService
{
    public function __construct(
        protected ExchangeRateRepository $repository,
        protected ParameterBagInterface $parameterBag,
        protected HttpClientInterface $httpClient,
        protected LoggerInterface $parserLogger
    ) {
    }

    /**
     * @throws ParseExchangeRateException
     */
    protected function getThreshold(): int
    {
        $threshold = $this->parameterBag->get('EXCHANGE_RATE_THRESHOLD');
        if (!$threshold) {
            throw new ParseExchangeRateException('Need set EXCHANGE_RATE_THRESHOLD');
        }
        return (int) $threshold;
    }

    protected function createExchangeRate(CurrencyEnum $currency, $buy = 0, $sale = 0): ExchangeRate
    {
        $oneCurrency = new ExchangeRate();
        return $oneCurrency
            ->setCreatedAt()
            ->setCurrency($currency)
            ->setBuy($buy)
            ->setSale($sale);
    }

    /**
     * @param  $currencies CurrencyEnum[]
     * @return array<CurrencyEnum,ExchangeRate>
     * @throws QueryException
     */
    protected function prepareCurrentValues(array $currencies): array
    {
        $exchangeRates = [];
        $currentValues = $this->repository->findCurrentByCurrency($currencies);
        foreach ($currencies as $currency) {
            $oneCurrency = $currentValues[$currency->name] ?? null;
            if (!$oneCurrency) {
                $oneCurrency = $this->createExchangeRate($currency);
            }
            $exchangeRates[$currency->name] = $oneCurrency;
        }
        return $exchangeRates;
    }

    /**
     * @param $banks BankEnum[]
     * @param  array<CurrencyEnum,ExchangeRate>  $currentValues
     * @return array<CurrencyEnum,ExchangeRateHistoryDto[]>
     */
    protected function getBanksChanges(array $banks, array $currentValues): array
    {
        foreach ($banks as $bank) {
            $oneBankChanges = $this->getParser($bank)->trackingChange($currentValues);
            $log = array_map(
                fn(ExchangeRateWithThresholdChange $exC) => [
                    $exC->currency->value => $exC->buy.'/'.$exC->sale,
                    'changes' => $exC->buyThreshold.'/'.$exC->saleThreshold,
                ],
                $oneBankChanges
            );
            $this->parserLogger->debug('Current currencies IN BANK '.$bank->value.' :', $log);
            foreach ($oneBankChanges as $bankChange) {
                $bankChanges[$bankChange->currency->name][] = new ExchangeRateHistoryDto(
                    $bank,
                    $bankChange->buyThreshold,
                    $bankChange->saleThreshold
                );
            }
        }
        return $bankChanges ?? [];
    }

    /**
     * @param  ExchangeRateHistoryDto[]  $exchangeRateHistoryDtos
     * @param  ExchangeRate  $exchangeRate
     * @return ExchangeRateHistory[]|null
     */
    public function prepareNewExchangeRateHistories(ExchangeRate $exchangeRate, array $exchangeRateHistoryDtos): ?array
    {
        $result = [];
        foreach ($exchangeRateHistoryDtos as $exchangeRateHistoryDto) {
            $lastValue = $exchangeRate->getLastExchangeRateHistories($exchangeRateHistoryDto->bank);
            if (
                !$lastValue ||
                (
                    $lastValue->getSaleThreshold() !== $exchangeRateHistoryDto->saleThreshold ||
                    $lastValue->getBuyThreshold() !== $exchangeRateHistoryDto->buyThreshold
                )
            ) {
                $exchangeRateHistory = new ExchangeRateHistory();
                $exchangeRateHistory
                    ->setBuyThreshold($exchangeRateHistoryDto->buyThreshold)
                    ->setSaleThreshold($exchangeRateHistoryDto->saleThreshold)
                    ->setBank($exchangeRateHistoryDto->bank);
                $result[] = $exchangeRateHistory;
            }
        }
        return count($result) ? $result : null;
    }

    protected function isChanged(int $threshold, int $buyThreshold, int $saleThreshold): bool
    {
        return abs($buyThreshold) >= $threshold || abs($saleThreshold) >= $threshold;
    }

    /**
     * @param  ExchangeRate  $exchangeRate
     * @param  ExchangeRateHistoryDto[]  $changes
     * @param  int  $threshold
     * @return ExchangeRateWithThresholdChange|null
     */
    protected function processingExchangeRate(
        ExchangeRate $exchangeRate,
        array $changes,
        int $threshold
    ): ?ExchangeRateWithThresholdChange {
        $currencyHistories = $this->prepareNewExchangeRateHistories($exchangeRate, $changes);
        if (!$currencyHistories) {
            return null;
        }
        $result = null;
        sort($currencyHistories);
        foreach ($currencyHistories as $history) {
            if (!$result && $this->isChanged($threshold, $history->getBuyThreshold(), $history->getSaleThreshold())) {
                $result = new ExchangeRateWithThresholdChange(
                    $exchangeRate->getCurrency(),
                    $exchangeRate->getBuy(),
                    $exchangeRate->getSale(),
                    $history->getBuyThreshold(),
                    $history->getSaleThreshold()
                );
            }
        }
        $newExchangeRate = null;
        $logMsg = 'Currency '.$exchangeRate->getCurrency()->value;
        if ($result) {
            $newExchangeRate = $this->createExchangeRate(
                $exchangeRate->getCurrency(),
                $result->getNewBuy(),
                $result->getNewSale()
            );
            $exchangeRate->setStatus(ExchangeRateStatusEnum::Old);
            $logMsg .= ' has changes on '.$result->getNewBuy().'/'.$result->getNewSale();
        } else {
            $logMsg .= 'has not changes! And save ='.count($currencyHistories);
        }
        $this->parserLogger->debug($logMsg);
        $this->repository->saveExchangeRates($exchangeRate, $currencyHistories, $newExchangeRate);
        return $result;
    }

    /**
     * @param $banks BankEnum[]
     * @param $currencies CurrencyEnum[]
     * @return ExchangeRateWithThresholdChange[]|null
     * @throws QueryException
     */
    public function getChanges(array $banks, array $currencies): array|null
    {
        $currentValues = $this->prepareCurrentValues($currencies);
        $this->parserLogger->debug(
            'Current currencies:',
            array_map(
                fn(ExchangeRate $ex) => $ex->getBuy().'/'.$ex->getSale(), $currentValues
            )
        );
        $bankChanges = $this->getBanksChanges($banks, $currentValues);
        $threshold = $this->getThreshold();
        $result = null;
        foreach ($currentValues as $exchangeRate) {
            $changes = $bankChanges[$exchangeRate->getCurrency()->name];
            $change = $this->processingExchangeRate($exchangeRate, $changes, $threshold);
            if ($change) {
                $result[] = $change;
            }
        }
        return $result;
    }

    /**
     * @throws ParseExchangeRateException
     */
    protected function getParser(BankEnum $bank): BankParserServiceInterface
    {
        $params = [$this->httpClient, $this->parameterBag];
        return match ($bank) {
            BankEnum::MONOBANK => new MonobankParserService(...$params),
            BankEnum::PRIVAT_BANK => new PrivatBankParserService(...$params),
            default => throw new ParseExchangeRateException('Not support bank '.$bank->name),
        };
    }
}