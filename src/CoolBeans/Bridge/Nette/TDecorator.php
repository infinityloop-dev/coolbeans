<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Bridge\Nette;

use Infinityloop\CoolBeans\PrimaryKey\PrimaryKey;

trait TNetteDecorator
{
    use \Infinityloop\CoolBeans\Decorator\TCommon;

    protected \Infinityloop\CoolBeans\Bridge\Nette\DataSource $dataSource;

    public function getRow(PrimaryKey $key) : \Infinityloop\CoolBeans\Bridge\Nette\ActiveRow
    {
        return $this->dataSource->getRow($key);
    }

    public function findAll() : \Infinityloop\CoolBeans\Bridge\Nette\Selection
    {
        return $this->dataSource->findAll();
    }

    public function findByArray(array $filter) : \Infinityloop\CoolBeans\Bridge\Nette\Selection
    {
        return $this->dataSource->findByArray($filter);
    }
}
