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

namespace App\Enum;

enum BankEnum: string
{
    case PRIVAT_BANK = 'PRIVAT_BANK';
    case MONOBANK = 'MONOBANK';
}