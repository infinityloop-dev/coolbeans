<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Decorator;

use Infinityloop\CoolBeans\PrimaryKey\PrimaryKey;

trait TDecorator
{
    use \Infinityloop\CoolBeans\Decorator\TCommon;

    protected \Infinityloop\CoolBeans\Contract\DataSource $dataSource;
    
    public function getRow(PrimaryKey $key) : \Infinityloop\CoolBeans\Contract\Row
    {
        return $this->dataSource->getRow($key);
    }

    public function findAll() : \Infinityloop\CoolBeans\Contract\Selection
    {
        return $this->dataSource->findAll();
    }

    public function findByArray(array $filter) : \Infinityloop\CoolBeans\Contract\Selection
    {
        return $this->dataSource->findByArray($filter);
    }
}
