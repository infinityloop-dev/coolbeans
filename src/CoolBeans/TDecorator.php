<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans;

use Infinityloop\CoolBeans\PrimaryKey\PrimaryKey;

trait TDecorator
{
    use \Infinityloop\CoolBeans\Decorator\TCommon;

    protected \Infinityloop\CoolBeans\DataSource $dataSource;

    public function getRow(PrimaryKey $key) : \Infinityloop\CoolBeans\Bean
    {
        return $this->dataSource->getRow($key);
    }

    public function findAll() : \Infinityloop\CoolBeans\Selection
    {
        return $this->dataSource->findAll();
    }

    public function findByArray(array $filter) : \Infinityloop\CoolBeans\Selection
    {
        return $this->dataSource->findByArray($filter);
    }
}
