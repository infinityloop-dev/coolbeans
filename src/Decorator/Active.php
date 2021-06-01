<?php

declare(strict_types = 1);

namespace CoolBeans\Decorator;

final class Active implements \CoolBeans\Contract\DataSource
{
    use \Nette\SmartObject;
    use \CoolBeans\Decorator\TDecorator;

    public function __construct(\CoolBeans\Contract\DataSource $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function getRow(\CoolBeans\Contract\PrimaryKey $key) : \CoolBeans\Contract\Row
    {
        $row = $this->dataSource->getRow($key);

        if ($row->active === -1) {
            throw new \CoolBeans\Exception\RowNotFound('Row with key [' . $key->printValue() . '] was deleted.');
        }

        return $row;
    }

    public function findAll() : \CoolBeans\Contract\Selection
    {
        return $this->dataSource->findAll()
            ->where($this->getName() . '.active >= ?', 0);
    }

    public function findByArray(array $filter) : \CoolBeans\Contract\Selection
    {
        return $this->dataSource->findByArray($filter)
            ->where($this->getName() . '.active >= ?', 0);
    }

    public function delete(\CoolBeans\Contract\PrimaryKey $key) : \CoolBeans\Result\Delete
    {
        $this->dataSource->update($key, ['active' => -1]);

        return new \CoolBeans\Result\Delete($key);
    }

    public function deleteByArray(array $filter) : \CoolBeans\Result\DeleteByArray
    {
        $result = $this->dataSource->updateByArray($filter, ['active' => -1]);

        return new \CoolBeans\Result\DeleteByArray($result->changedIds);
    }
}
