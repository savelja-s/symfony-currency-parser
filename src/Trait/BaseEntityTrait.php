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

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;

trait BaseEntityTrait
{
    #[Column, Id, GeneratedValue]
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}