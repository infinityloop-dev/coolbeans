<?php

declare(strict_types = 1);

namespace CoolBeans\PrimaryKey;

use CoolBeans\Contract\PrimaryKey;

final class ArrayPrimaryKey extends PrimaryKey
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
    
    public function printValue(): string
    {
        return \implode('|', $this->value);
    }

    public function getName() : string
    {
        return \implode('|', \array_keys($this->value));
    }

    public function equals(PrimaryKey $compare) : bool
    {
        return $compare instanceof self && $this->getValue() === $compare->getValue();
    }
}
