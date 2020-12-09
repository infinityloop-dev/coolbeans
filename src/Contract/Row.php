<?php

declare(strict_types = 1);

namespace CoolBeans\Contract;

interface Row extends \ArrayAccess
{
    public function getTableName() : string;

    public function toArray() : array;

    public function getPrimaryKey() : \CoolBeans\Contract\PrimaryKey;
}
