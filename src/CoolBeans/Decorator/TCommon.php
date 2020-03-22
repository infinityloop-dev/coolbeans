<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Decorator;

use Infinityloop\CoolBeans\Contract\PrimaryKey;

/**
 * Trait TCommon
 * 
 * @property \Infinityloop\CoolBeans\Contract\DataSource $dataSource
 */
trait TCommon
{
    public function getName() : string
    {
        return $this->dataSource->getName();
    }

    public function insert(array $data) : \Infinityloop\CoolBeans\Result\Insert
    {
        return $this->dataSource->insert($data);
    }

    public function insertMultiple(array $data) : \Infinityloop\CoolBeans\Result\InsertMultiple
    {
        return $this->dataSource->insertMultiple($data);
    }

    public function update(PrimaryKey $key, array $data) : \Infinityloop\CoolBeans\Result\Update
    {
        return $this->dataSource->update($key, $data);
    }

    public function updateByArray(array $filter, array $data) : \Infinityloop\CoolBeans\Result\UpdateByArray
    {
        return $this->dataSource->updateByArray($filter, $data);
    }

    public function delete(PrimaryKey $key) : \Infinityloop\CoolBeans\Result\Delete
    {
        return $this->dataSource->delete($key);
    }

    public function deleteByArray(array $filter) : \Infinityloop\CoolBeans\Result\DeleteByArray
    {
        return $this->dataSource->deleteByArray($filter);
    }

    public function upsert(?PrimaryKey $key, array $values) : \Infinityloop\CoolBeans\Contract\Result
    {
        return $this->dataSource->upsert($key, $values);
    }

    public function transaction(callable $function)
    {
        return $this->dataSource->transaction($function);
    }
}
