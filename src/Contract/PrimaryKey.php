<?php

declare(strict_types = 1);

namespace CoolBeans\Contract;

abstract class PrimaryKey
{
    use \Nette\SmartObject;
    
    abstract public function getValue();
    
    abstract public function printValue() : string;

    abstract public function getName() : string;

    abstract public function equals(PrimaryKey $compare) : bool;

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

    public static function fromSelection(\Nette\Database\Table\Selection $selection) : array
    {
        $primaryKeys = [];

        foreach ($selection as $row) {
            $primaryKeys[] = PrimaryKey::create($row);
        }

        return $primaryKeys;
    }
}
