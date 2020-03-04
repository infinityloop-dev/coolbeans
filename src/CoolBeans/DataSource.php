<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans;

use Infinityloop\CoolBeans\PrimaryKey\PrimaryKey;

interface DataSource
{
    /**
     * Returns table name
     */
    public function getName() : string;

    /**
     * Returns row from table by its id
     */
    public function getRow(PrimaryKey $key);

    /**
     * Returns selection of all entries
     */
    public function findAll() : \Iterator;

    /**
     * Returns selection of entries found by associative array
     */
    public function findByArray(array $filter) : \Iterator;

    /**
     * Inserts data into table
     */
    public function insert(array $data) : PrimaryKey;

    /**
     * Updates row
     */
    public function update(PrimaryKey $key, array $data) : PrimaryKey;

    /**
     * Deletes row
     */
    public function delete(PrimaryKey $key) : void;

    /**
     * Inserts data if no key provided, updates otherwise
     */
    public function upsert(?PrimaryKey $key, array $values) : PrimaryKey;
}
