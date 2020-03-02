<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\PrimaryKey;

abstract class PrimaryKey
{
    abstract public function getValue();

    abstract public function getName() : string;

    public static function create(\Nette\Database\Table\ActiveRow $activeRow) : ?self
    {
        $primary = $activeRow->getPrimary();

        if (\is_int($primary)) {
            return new IntPrimaryKey($primary);
        }

        if (\is_array($primary)) {
            return new ArrayPrimaryKey($primary);
        }

        return null;
    }
}
