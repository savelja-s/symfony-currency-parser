<?php


declare(strict_types=1);

namespace App\Enum;

enum CurrencyISO4217Enum: string
{
    case USD = "840";
    case EUR = "978";
    case UAH = "980";

    public static function fromName(string $name)
    {
        return constant("self::$name");
    }
}