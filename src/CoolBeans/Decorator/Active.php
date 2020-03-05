<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Decorator;

use Infinityloop\CoolBeans\PrimaryKey\PrimaryKey;

class Active implements \Infinityloop\CoolBeans\DataSource
{
    use \Nette\SmartObject;
    use \Infinityloop\CoolBeans\Decorator\TDecorator;

    public function __construct(\Infinityloop\CoolBeans\DataSource $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function getRow(PrimaryKey $key)
    {
        $row = $this->dataSource->getRow($key);

        if ($row->active === -1) {
            throw new \Infinityloop\CoolBeans\Exception\RowNotFound('Row with key [' . $key->printValue() . '] was deleted.');
        }

        return $row;
    }

    public function findAll() : \Iterator
    {
        return $this->dataSource->findAll()->where('active >= ?', 0);
    }

    public function findByArray(array $filter) : \Iterator
    {
        return $this->dataSource->findByArray($filter)->where('active >= ?', 0);
    }

    public function delete(PrimaryKey $key) : void
    {
        $this->dataSource->update($key, ['active' => -1]);
    }

    public function deleteByArray(array $filter): int
    {
        return $this->dataSource->updateByArray($filter, ['active' => -1]);
    }
}
