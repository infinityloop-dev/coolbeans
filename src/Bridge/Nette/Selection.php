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
        if (($key = current($this->keys)) !== false) {
            return $this->data[$key];
        } else {
            return null;
        }
    }

    protected function createRow(array $row) : \CoolBeans\Bridge\Nette\ActiveRow
    {
        return new \CoolBeans\Bridge\Nette\ActiveRow($row, $this);
    }
}
