<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\PrimaryKey;

class IntPrimaryKey implements PrimaryKey
{
    private int $value;
    private string $name;

    public function __construct(int $key, string $name = 'id')
    {
        $this->value = $key;
        $this->name = $name;
    }

    public function getValue() : int
    {
        return $this->value;
    }

    public function getName() : string
    {
        return $this->name;
    }
}
