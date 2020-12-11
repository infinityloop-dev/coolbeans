<?php

declare(strict_types = 1);

namespace CoolBeans\Bridge\Nette;

use \CoolBeans\Contract\PrimaryKey;

trait TDecorator
{
    use \CoolBeans\Decorator\TCommon;

    protected \CoolBeans\Bridge\Nette\DataSource $dataSource;

    public function getRow(PrimaryKey $key) : \CoolBeans\Bridge\Nette\ActiveRow
    {
        return $this->dataSource->getRow($key);
    }

    public function findAll() : \CoolBeans\Bridge\Nette\Selection
    {
        return $this->dataSource->findAll();
    }

    public function findByArray(array $filter) : \CoolBeans\Bridge\Nette\Selection
    {
        return $this->dataSource->findByArray($filter);
    }
}
