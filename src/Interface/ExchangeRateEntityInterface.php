<?php


declare(strict_types=1);

namespace App\Interface;

interface ExchangeRateEntityInterface
{
    public function getId(): ?int;

    public function setCreatedAt(\DateTime $createdAt): static;

    public function getCreatedAt(): ?\DateTime;
}