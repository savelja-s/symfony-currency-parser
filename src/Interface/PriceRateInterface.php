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

namespace App\Interface;

interface PriceRateInterface
{
    public function getCreatedAt(): ?\DateTime;

    public function getBuy(): ?int;

    public function setBuy(int $buy): static;

    public function getSell(): ?int;

    public function setSell(int $sell): static;
}