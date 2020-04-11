<?php

declare(strict_types = 1);

namespace CoolBeans\Decorator;

use CoolBeans\Contract\PrimaryKey;

trait TDecorator
{
    use \CoolBeans\Decorator\TCommon;

    protected \CoolBeans\Contract\DataSource $dataSource;
    
    public function getRow(PrimaryKey $key) : \CoolBeans\Contract\Row
    {
        return $this->dataSource->getRow($key);
    }

    public function findAll() : \CoolBeans\Contract\Selection
    {
        return $this->dataSource->findAll();
    }

    public function findByArray(array $filter) : \CoolBeans\Contract\Selection
    {
        return $this->dataSource->findByArray($filter);
    }
}
