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
 * @date      04.02.24
 */

declare(strict_types=1);

namespace App\Dto;

use App\Enum\BankEnum;

class ExchangeRateHistoryDto
{
    public function __construct(public BankEnum $bank, public int $buyThreshold, public int $saleThreshold)
    {
    }
}