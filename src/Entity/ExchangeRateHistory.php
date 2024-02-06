<?php

namespace App\Entity;

use App\Enum\BankEnum;
use App\Interface\ExchangeRateEntityInterface;
use App\Repository\ExchangeRateHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExchangeRateHistoryRepository::class)]
class ExchangeRateHistory extends AbstractExchangeRateEntity implements ExchangeRateEntityInterface
{
    #[ORM\Column(type: Types::STRING, length: 15, enumType: BankEnum::class)]
    private BankEnum $bank;

    #[ORM\Column(type: Types::INTEGER)]
    private int $buyThreshold;

    #[ORM\Column(type: Types::INTEGER)]
    private int $saleThreshold;

    #[ORM\ManyToOne(inversedBy: 'exchangeRateHistories')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ExchangeRate $exchangeRate;

    public function getBank(): BankEnum
    {
        return $this->bank;
    }

    public function setBank(BankEnum $bank): static
    {
        $this->bank = $bank;
        return $this;
    }

    public function getBuyThreshold(): ?int
    {
        return $this->buyThreshold;
    }

    public function setBuyThreshold(int $buyThreshold): static
    {
        $this->buyThreshold = $buyThreshold;
        return $this;
    }

    public function getSaleThreshold(): ?int
    {
        return $this->saleThreshold;
    }

    public function setSaleThreshold(int $saleThreshold): static
    {
        $this->saleThreshold = $saleThreshold;
        return $this;
    }

    public function getExchangeRate(): ?ExchangeRate
    {
        return $this->exchangeRate;
    }

    public function setExchangeRate(?ExchangeRate $exchangeRate): static
    {
        $this->exchangeRate = $exchangeRate;
        return $this;
    }
}
