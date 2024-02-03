<?php

namespace App\Entity;

use App\Enum\BankEnum;
use App\Enum\CurrencyEnum;
use App\Interface\BaseEntityInterface;
use App\Interface\ExchangeRateInterface;
use App\Interface\PriceRateInterface;
use App\Repository\ExchangeRateRepository;
use App\Trait\BaseEntityTrait;
use App\Trait\PriceRateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExchangeRateRepository::class)]
class ExchangeRate implements ExchangeRateInterface, PriceRateInterface, BaseEntityInterface
{
    use PriceRateTrait;
    use BaseEntityTrait;

    #[ORM\Column(type: Types::STRING, length: 5, enumType: CurrencyEnum::class)]
    private CurrencyEnum $currency;

    #[ORM\Column(type: Types::STRING, length: 15, enumType: BankEnum::class)]
    private BankEnum $bank;

    #[ORM\OneToMany(mappedBy: 'exchangeRate', targetEntity: ExchangeRateHistory::class, orphanRemoval: true)]
    private Collection $exchangeRateHistories;

    public function __construct()
    {
        $this->exchangeRateHistories = new ArrayCollection();
    }

    public function getCurrency(): CurrencyEnum
    {
        return $this->currency;
    }

    public function setCurrency(CurrencyEnum $currency): static
    {
        $this->currency = $currency;
        return $this;
    }

    public function getBank(): BankEnum
    {
        return $this->bank;
    }

    public function setBank(BankEnum $bank): static
    {
        $this->bank = $bank;
        return $this;
    }

    /**
     * @return Collection<int, ExchangeRateHistory>
     */
    public function getExchangeRateHistories(): Collection
    {
        return $this->exchangeRateHistories;
    }

    public function addExchangeRateHistory(ExchangeRateHistory $exchangeRateHistory): static
    {
        if (!$this->exchangeRateHistories->contains($exchangeRateHistory)) {
            $this->exchangeRateHistories->add($exchangeRateHistory);
            $exchangeRateHistory->setExchangeRate($this);
        }
        return $this;
    }

    public function removeExchangeRateHistory(ExchangeRateHistory $exchangeRateHistory): static
    {
        if ($this->exchangeRateHistories->removeElement($exchangeRateHistory)) {
            // set the owning side to null (unless already changed)
            if ($exchangeRateHistory->getExchangeRate() === $this) {
                $exchangeRateHistory->setExchangeRate(null);
            }
        }
        return $this;
    }
}
