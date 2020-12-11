<?php

declare(strict_types = 1);

namespace CoolBeans\PrimaryKey;

final class IntPrimaryKey extends \CoolBeans\Contract\PrimaryKey
{
    private int $value;
    private string $name;

    public function __construct(int $key, string $name = 'id')
    {
        if ($key <= 0) {
            throw new \CoolBeans\Exception\InvalidFunctionParameters('Primary key must be positive integer.');
        }

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

    public function equals(\CoolBeans\Contract\PrimaryKey $compare) : bool
    {
        return $compare instanceof self && $this->getValue() === $compare->getValue() && $this->getName() === $compare->getName();
    }
}
