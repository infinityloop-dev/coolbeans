<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Contract;

interface Row extends \ArrayAccess
{
    public function toArray() : array;

    public function getPrimaryKey() : ?\Infinityloop\CoolBeans\PrimaryKey\PrimaryKey;

    public function __get(string $name);

    public function __isset($name);
}
