<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Contract;

use Infinityloop\CoolBeans\Contract\PrimaryKey;

interface DataSource
{
    /**
     * Returns table name.
     */
    public function getName() : string;

    /**
     * Returns row from table by its id.
     */
    public function getRow(PrimaryKey $key) : \Infinityloop\CoolBeans\Contract\Row;

    /**
     * Returns selection of all entries.
     */
    public function findAll() : \Infinityloop\CoolBeans\Contract\Selection;

    /**
     * Returns selection of entries found by associative array.
     */
    public function findByArray(array $filter) : \Infinityloop\CoolBeans\Contract\Selection;

    /**
     * Inserts data into table.
     */
    public function insert(array $data) : \Infinityloop\CoolBeans\Result\Insert;

    /**
     * Inserts multiple rows into table.
     * Returns array of new Primary keys.
     */
    public function insertMultiple(array $data) : \Infinityloop\CoolBeans\Result\InsertMultiple;

    /**
     * Updates row.
     */
    public function update(PrimaryKey $key, array $data) : \Infinityloop\CoolBeans\Result\Update;

    /**
     * Updates selection of entries found by associative array.
     * Returns number of affected rows.
     */
    public function updateByArray(array $filter, array $data) : \Infinityloop\CoolBeans\Result\UpdateByArray;

    /**
     * Deletes row
     */
    public function delete(PrimaryKey $key) : \Infinityloop\CoolBeans\Result\Delete;

    /**
     * Deletes selection of entries found by associative array.
     * Returns number of affected rows.
     */
    public function deleteByArray(array $filter) : \Infinityloop\CoolBeans\Result\DeleteByArray;

    /**
     * Inserts data if no key provided, updates otherwise.
     */
    public function upsert(?PrimaryKey $key, array $values) : \Infinityloop\CoolBeans\Contract\Result;

    /**
     * Executes function enclosed in PDO transaction.
     */
    public function transaction(callable $function);
}
