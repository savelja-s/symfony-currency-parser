<?php


declare(strict_types=1);

namespace App\Enum;

use App\Trait\EnumToArrayTrait;

enum BankEnum: string
{
    use EnumToArrayTrait;

    case PRIVAT_BANK = 'PRIVAT_BANK';
    case MONOBANK = 'MONOBANK';
}
