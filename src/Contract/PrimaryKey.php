<?php

declare(strict_types = 1);

namespace CoolBeans\Contract;

abstract class PrimaryKey
{
    use \Nette\SmartObject;

    public static function create(\Nette\Database\Table\ActiveRow $activeRow) : self
    {
        $primary = $activeRow->getPrimary(false);

        if (\is_int($primary)) {
            return new \CoolBeans\PrimaryKey\IntPrimaryKey($primary, $activeRow->getTable()->getPrimary(false));
        }

        if (\is_array($primary)) {
            return new \CoolBeans\PrimaryKey\ArrayPrimaryKey($primary);
        }

        throw new \CoolBeans\Exception\MissingPrimaryKey('Table [' . $activeRow->getTable()->getName() . '] has no primary key.');
    }

    abstract public function getValue() : int|array;

    abstract public function printValue() : string;

    abstract public function getName() : string;

    abstract public function equals(PrimaryKey $compare) : bool;

    public static function fromSelection(\Nette\Database\Table\Selection $selection) : array
    {
        $primaryKeys = [];

        foreach ($selection as $row) {
            $primaryKeys[] = self::create($row);
        }

        return $primaryKeys;
    }
}
