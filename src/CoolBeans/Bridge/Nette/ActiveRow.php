<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Bridge\Nette;

class ActiveRow extends \Nette\Database\Table\ActiveRow implements \Infinityloop\CoolBeans\Contract\Row
{
    protected ?\Infinityloop\CoolBeans\Contract\PrimaryKey $primaryKey = null;

    public function getTableName(): string
    {
        return $this->getTable()->getName();
    }

    public function getPrimaryKey(): \Infinityloop\CoolBeans\Contract\PrimaryKey
    {
        if (!$this->primaryKey instanceof \Infinityloop\CoolBeans\Contract\PrimaryKey) {
            $this->primaryKey = \Infinityloop\CoolBeans\Contract\PrimaryKey::create($this);
        }

        return $this->primaryKey;
    }
}
