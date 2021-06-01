<?php

declare(strict_types = 1);

namespace CoolBeans;

trait TDecorator
{
    use \CoolBeans\Decorator\TCommon;

    protected \CoolBeans\DataSource $dataSource;

    public function getRow(\CoolBeans\Contract\PrimaryKey $key) : \CoolBeans\Bean
    {
        return $this->dataSource->getRow($key);
    }

    public function findAll() : \CoolBeans\Selection
    {
        return $this->dataSource->findAll();
    }

    public function findByArray(array $filter) : \CoolBeans\Selection
    {
        return $this->dataSource->findByArray($filter);
    }
}
