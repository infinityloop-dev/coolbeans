<?php

declare(strict_types = 1);

namespace CoolBeans\Bridge\Nette;

class Selection extends \Nette\Database\Table\Selection implements \CoolBeans\Contract\Selection
{
    public function where($condition, ...$params) : static
    {
        return parent::where($condition, ...$params);
    }

    public function fetch() : ?\CoolBeans\Bridge\Nette\ActiveRow
    {
        return parent::fetch();
    }

    public function key() : string|int
    {
        return parent::key();
    }

    public function getTableName() : string
    {
        return $this->getName();
    }

    public function current() : ?\CoolBeans\Bridge\Nette\ActiveRow
    {
        return parent::current()
            ?: null;
    }

    protected function createRow(array $row) : \CoolBeans\Bridge\Nette\ActiveRow
    {
        return new \CoolBeans\Bridge\Nette\ActiveRow($row, $this);
    }
}
