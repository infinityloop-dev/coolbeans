<?php

declare(strict_types = 1);

namespace CoolBeans\Decorator;

trait TDecorator
{
    use \CoolBeans\Decorator\TCommon;

    protected \CoolBeans\Contract\DataSource $dataSource;

    public function getRow(\CoolBeans\Contract\PrimaryKey $key) : \CoolBeans\Contract\Row
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
