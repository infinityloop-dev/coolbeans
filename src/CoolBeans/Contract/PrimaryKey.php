<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Contract;

abstract class PrimaryKey
{
    use \Nette\SmartObject;
    
    abstract public function getValue();
    
    abstract public function printValue() : string;

    abstract public function getName() : string;

    public static function create(\Nette\Database\Table\ActiveRow $activeRow) : self
    {
        $primary = $activeRow->getPrimary(false);

        if (\is_int($primary)) {
            return new \Infinityloop\CoolBeans\PrimaryKey\IntPrimaryKey($primary);
        }

        if (\is_array($primary)) {
            return new \Infinityloop\CoolBeans\PrimaryKey\ArrayPrimaryKey($primary);
        }

        throw new \Infinityloop\CoolBeans\Exception\MissingPrimaryKey('Table [' . $activeRow->getTable()->getName() . '] has no primary key.');
    }

    public static function fromSelection(\Nette\Database\Table\Selection $selection) : array
    {
        $primaryKeys = [];

        foreach ($selection as $row) {
            $primaryKeys[] = PrimaryKey::create($row);
        }

        return $primaryKeys;
    }
}
