<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Decorator;

use Infinityloop\CoolBeans\PrimaryKey\PrimaryKey;

trait TDecorator
{
    use \Infinityloop\CoolBeans\Decorator\TCommon;

    protected \Infinityloop\CoolBeans\DataSource $dataSource;
    
    public function getRow(PrimaryKey $key)
    {
        return $this->dataSource->getRow($key);
    }

    public function findAll() : \Iterator
    {
        return $this->dataSource->findAll();
    }

    public function findByArray(array $filter) : \Iterator
    {
        return $this->dataSource->findByArray($filter);
    }
}
