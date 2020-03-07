<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Bridge\Nette;

class Selection extends \Nette\Database\Table\Selection implements \Infinityloop\CoolBeans\Contract\Selection
{
    public function getTableName() : string
    {
        return $this->getName();
    }

    public function current() : ?\Infinityloop\CoolBeans\Bridge\Nette\ActiveRow
    {
        if (($key = current($this->keys)) !== false) {
            return $this->data[$key];
        } else {
            return null;
        }
    }

    protected function createRow(array $row) : \Infinityloop\CoolBeans\Bridge\Nette\ActiveRow
    {
        return new \Infinityloop\CoolBeans\Bridge\Nette\ActiveRow($row, $this);
    }
}
