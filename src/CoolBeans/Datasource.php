<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans;

use Infinityloop\CoolBeans\PrimaryKey\PrimaryKey;

abstract class DataSource
{
    /**
     * Returns table name
     */
    abstract public function getName() : string;

    /**
     * Returns row from table by its id
     */
    abstract public function getRow(PrimaryKey $key);

    /**
     * Returns selection of all entries
     */
    abstract public function findAll() : \Iterator;

    /**
     * Returns selection of entries found by associative array
     */
    abstract public function findByArray(array $filter) : \Iterator;

    /**
     * Inserts data into table
     */
    abstract public function insert(array $data) : PrimaryKey;

    /**
     * Updates row
     */
    abstract public function update(PrimaryKey $key, array $data) : PrimaryKey;

    /**
     * Deletes row
     */
    abstract public function delete(PrimaryKey $key) : void;

    /**
     * Inserts data if no key provided, updates otherwise
     */
    public function upsert(?PrimaryKey $key, array $values) : PrimaryKey
    {
        if ($key instanceof PrimaryKey) {
            $this->update($key, $values);

            return $key;
        }

        return $this->insert($values);
    }
}
