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

namespace App\Exception;

class ParseExchangeRateException extends \RuntimeException
{
    public function __construct(string $message = 'ParseExchangeRateException', ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}