<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Decorator;

use Infinityloop\CoolBeans\PrimaryKey\PrimaryKey;

trait TNetteDecorator
{
    use \Infinityloop\CoolBeans\Decorator\TCommon;

    protected \Infinityloop\CoolBeans\NetteDataSource $dataSource;

    public function getRow(PrimaryKey $key) : \Nette\Database\Table\ActiveRow
    {
        return $this->dataSource->getRow($key);
    }

    public function findAll() : \Nette\Database\Table\Selection
    {
        return $this->dataSource->findAll();
    }

    public function findByArray(array $filter) : \Nette\Database\Table\Selection
    {
        return $this->dataSource->findByArray($filter);
    }
}
