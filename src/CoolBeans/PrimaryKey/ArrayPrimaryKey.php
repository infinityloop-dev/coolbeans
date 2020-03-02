<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\PrimaryKey;

class ArrayPrimaryKey extends PrimaryKey
{
    private array $value;

    public function __construct(array $key)
    {
        $this->value = $key;
    }

    public function getValue() : array
    {
        return $this->value;
    }

    public function getName() : string
    {
        return \implode('|', \array_keys($this->value));
    }
}
