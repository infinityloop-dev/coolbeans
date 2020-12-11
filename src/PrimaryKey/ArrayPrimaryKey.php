<?php

declare(strict_types = 1);

namespace CoolBeans\PrimaryKey;

final class ArrayPrimaryKey extends \CoolBeans\Contract\PrimaryKey
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

    public function printValue() : string
    {
        return \implode('|', $this->value);
    }

    public function getName() : string
    {
        return \implode('|', \array_keys($this->value));
    }

    public function equals(\CoolBeans\Contract\PrimaryKey $compare) : bool
    {
        return $compare instanceof self && $this->getValue() === $compare->getValue();
    }
}
