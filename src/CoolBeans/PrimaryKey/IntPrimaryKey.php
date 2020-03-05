<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\PrimaryKey;

final class IntPrimaryKey extends PrimaryKey
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
    
    public function printValue() : string
    {
        return (string) $this->value;
    }

    public function getName() : string
    {
        return $this->name;
    }
}
