<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Bridge\Nette;

class ActiveRow extends \Nette\Database\Table\ActiveRow implements \Infinityloop\CoolBeans\Contract\Row
{
    protected ?\Infinityloop\CoolBeans\PrimaryKey\PrimaryKey $primaryKey = null;

    public function getTableName(): string
    {
        return $this->getTable()->getName();
    }

    public function getPrimaryKey(): \Infinityloop\CoolBeans\PrimaryKey\PrimaryKey
    {
        if (!$this->primaryKey instanceof \Infinityloop\CoolBeans\PrimaryKey\PrimaryKey) {
            $this->primaryKey = \Infinityloop\CoolBeans\PrimaryKey\PrimaryKey::create($this);
        }

        return $this->primaryKey;
    }
}
