<?php

namespace App\Entity;

use App\Interface\BaseEntityInterface;
use App\Interface\PriceRateInterface;
use App\Repository\ExchangeRateHistoryRepository;
use App\Trait\BaseEntityTrait;
use App\Trait\PriceRateTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExchangeRateHistoryRepository::class)]
class ExchangeRateHistory implements PriceRateInterface, BaseEntityInterface
{
    use PriceRateTrait;
    use BaseEntityTrait;

    #[ORM\ManyToOne(inversedBy: 'exchangeRateHistories')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ExchangeRate $exchangeRate;

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
