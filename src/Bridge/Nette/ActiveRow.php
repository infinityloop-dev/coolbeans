<?php

declare(strict_types = 1);

namespace CoolBeans\Bridge\Nette;

class ActiveRow extends \Nette\Database\Table\ActiveRow implements \CoolBeans\Contract\Row
{
    protected ?\CoolBeans\Contract\PrimaryKey $primaryKey = null;

    public function getTableName(): string
    {
        return $this->getTable()->getName();
    }

    public function getPrimaryKey(): \CoolBeans\Contract\PrimaryKey
    {
        if (!$this->primaryKey instanceof \CoolBeans\Contract\PrimaryKey) {
            $this->primaryKey = \CoolBeans\Contract\PrimaryKey::create($this);
        }

        return $this->primaryKey;
    }
}
