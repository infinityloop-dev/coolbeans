<?php

declare(strict_types = 1);

namespace CoolBeans\Decorator;

/**
 * Trait TCommon
 *
 * @property \CoolBeans\Contract\DataSource $dataSource
 */
trait TCommon
{
    public function getName() : string
    {
        return $this->dataSource->getName();
    }

    public function insert(array $data) : \CoolBeans\Result\Insert
    {
        return $this->dataSource->insert($data);
    }

    public function insertMultiple(array $data) : \CoolBeans\Result\InsertMultiple
    {
        return $this->dataSource->insertMultiple($data);
    }

    public function update(\CoolBeans\Contract\PrimaryKey $key, array $data) : \CoolBeans\Result\Update
    {
        return $this->dataSource->update($key, $data);
    }

    public function updateByArray(array $filter, array $data) : \CoolBeans\Result\UpdateByArray
    {
        return $this->dataSource->updateByArray($filter, $data);
    }

    public function delete(\CoolBeans\Contract\PrimaryKey $key) : \CoolBeans\Result\Delete
    {
        return $this->dataSource->delete($key);
    }

    public function deleteByArray(array $filter) : \CoolBeans\Result\DeleteByArray
    {
        return $this->dataSource->deleteByArray($filter);
    }

    public function upsert(?\CoolBeans\Contract\PrimaryKey $key, array $values) : \CoolBeans\Contract\Result
    {
        return $this->dataSource->upsert($key, $values);
    }

    public function transaction(callable $function) : mixed
    {
        return $this->dataSource->transaction($function);
    }
}
