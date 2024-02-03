<?php

/**
 * Created by PhpStorm.
 * PHP Version: 7.4.
 *
 * @category   <NameCategory>
 *
 * @author     Sergiy Savelev <sergiy.savelev@gmail.com>
 * @copyright  2014-2024 @Ovdaldk
 *
 * @see       <https://ovdal.dk>
 * @date      03.02.24
 */

declare(strict_types=1);

namespace App\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
trait PriceRateTrait
{
    #[ORM\Column(type: Types::INTEGER)]
    private int $buy;

    #[ORM\Column(type: Types::INTEGER)]
    private int $sell;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $created_at;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->created_at = new \DateTime();
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

    public function getSell(): ?int
    {
        return $this->sell;
    }

    public function setSell(int $sell): static
    {
        $this->sell = $sell;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }
}