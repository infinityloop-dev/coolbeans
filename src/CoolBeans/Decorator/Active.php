<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Decorator;

use Infinityloop\CoolBeans\Contract\PrimaryKey;

final class Active implements \Infinityloop\CoolBeans\Contract\DataSource
{
    use \Nette\SmartObject;
    use \Infinityloop\CoolBeans\Decorator\TDecorator;

    public function __construct(\Infinityloop\CoolBeans\Contract\DataSource $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function getRow(PrimaryKey $key) : \Infinityloop\CoolBeans\Contract\Row
    {
        $row = $this->dataSource->getRow($key);

        if ($row->active === -1) {
            throw new \Infinityloop\CoolBeans\Exception\RowNotFound('Row with key [' . $key->printValue() . '] was deleted.');
        }

        return $row;
    }

    public function findAll() : \Infinityloop\CoolBeans\Contract\Selection
    {
        return $this->dataSource->findAll()
            ->where($this->getName() . '.active >= ?', 0);
    }

    public function findByArray(array $filter) : \Infinityloop\CoolBeans\Contract\Selection
    {
        return $this->dataSource->findByArray($filter)
            ->where($this->getName() . '.active >= ?', 0);
    }

    public function delete(PrimaryKey $key) : \Infinityloop\CoolBeans\Result\Delete
    {
        $this->dataSource->update($key, ['active' => -1]);

        return new \Infinityloop\CoolBeans\Result\Delete($key);
    }

    public function deleteByArray(array $filter) : \Infinityloop\CoolBeans\Result\DeleteByArray
    {
        $result = $this->dataSource->updateByArray($filter, ['active' => -1]);

        return new \Infinityloop\CoolBeans\Result\DeleteByArray($result->changedIds);
    }
}
