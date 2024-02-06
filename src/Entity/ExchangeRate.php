<?php

namespace App\Entity;

use App\Enum\BankEnum;
use App\Enum\CurrencyEnum;
use App\Enum\ExchangeRateStatusEnum;
use App\Interface\ExchangeRateEntityInterface;
use App\Repository\ExchangeRateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExchangeRateRepository::class)]
class ExchangeRate extends AbstractExchangeRateEntity implements ExchangeRateEntityInterface
{
    #[ORM\Column(
        type: Types::STRING,
        length: 10,
        enumType: ExchangeRateStatusEnum::class
    )]
    private ExchangeRateStatusEnum $status = ExchangeRateStatusEnum::Current;

    #[ORM\Column(type: Types::INTEGER)]
    private int $buy;

    #[ORM\Column(type: Types::INTEGER)]
    private int $sale;

    #[ORM\Column(type: Types::STRING, length: 5, enumType: CurrencyEnum::class)]
    private CurrencyEnum $currency;

    #[ORM\OneToMany(
        mappedBy: 'exchangeRate',
        targetEntity: ExchangeRateHistory::class,
        cascade: ['all'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(["created_at" => "DESC"])]
    private Collection $exchangeRateHistories;

    public function __construct()
    {
        $this->exchangeRateHistories = new ArrayCollection();
    }

    public function getStatus(): ?ExchangeRateStatusEnum
    {
        return $this->status;
    }

    public function setStatus(ExchangeRateStatusEnum $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getBuy(): ?int
    {
        return $this->buy;
    }

    public function setBuy(int $buy): static
    {
        $this->buy = $buy;
        return $this;
    }

    public function getSale(): ?int
    {
        return $this->sale;
    }

    public function setSale(int $sale): static
    {
        $this->sale = $sale;
        return $this;
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

    /**
     * @return Collection<int, ExchangeRateHistory>
     */
    public function getExchangeRateHistories(): Collection
    {
        return $this->exchangeRateHistories;
    }

    /**
     * @param  BankEnum  $bank
     * @return null|ExchangeRateHistory
     */
    public function getLastExchangeRateHistories(BankEnum $bank): ?ExchangeRateHistory
    {
        return $this->exchangeRateHistories
            ->findFirst(static fn($key, ExchangeRateHistory $history) => $history->getBank() === $bank);
    }

    public function addExchangeRateHistory(ExchangeRateHistory $exchangeRateHistory): static
    {
        if (!$this->exchangeRateHistories->contains($exchangeRateHistory)) {
            $this->exchangeRateHistories->add($exchangeRateHistory);
            $exchangeRateHistory->setExchangeRate($this);
            $exchangeRateHistory->setCreatedAt(date_create());
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
