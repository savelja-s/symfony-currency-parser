<?php


declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

class AbstractExchangeRateEntity
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected ?\DateTime $created_at;

    #[ORM\Column, ORM\Id, ORM\GeneratedValue]
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCreatedAt(\DateTime $createdAt = new \DateTime()): static
    {
        $this->created_at = $createdAt;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }
}