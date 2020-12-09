<?php

declare(strict_types = 1);

namespace CoolBeans\Bridge\Nette;

class Selection extends \Nette\Database\Table\Selection implements \CoolBeans\Contract\Selection
{
    public function getTableName() : string
    {
        return $this->getName();
    }

    public function current() : ?\CoolBeans\Bridge\Nette\ActiveRow
    {
        return ($key = \current($this->keys)) !== false
            ? $this->data[$key]
            : null;
    }

    protected function createRow(array $row) : \CoolBeans\Bridge\Nette\ActiveRow
    {
        return new \CoolBeans\Bridge\Nette\ActiveRow($row, $this);
    }
}
