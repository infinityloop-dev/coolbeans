<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Decorator;

use Infinityloop\CoolBeans\PrimaryKey\PrimaryKey;

trait TDecorator
{
    protected \Infinityloop\CoolBeans\DataSource $dataSource;
    
    public function getName() : string
    {
        return $this->dataSource->getName();
    }
    
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
    
    public function insert(array $data) : PrimaryKey
    {
        return $this->dataSource->insert($data);
    }
    
    public function insertMultiple(array $data) : array
    {
        return $this->dataSource->insertMultiple($data);
    }
    
    public function update(PrimaryKey $key, array $data) : PrimaryKey
    {
        return $this->dataSource->update($key, $data);
    }
    
    public function updateByArray(array $filter, array $data) : int
    {
        return $this->dataSource->updateByArray($filter, $data);
    }

    public function delete(PrimaryKey $key) : void
    {
        $this->dataSource->delete($key);
    }

    public function deleteByArray(array $filter) : int
    {
        return $this->dataSource->deleteByArray($filter);
    }

    public function upsert(?PrimaryKey $key, array $values) : PrimaryKey
    {
        return $this->dataSource->upsert($key, $values);
    }
}
