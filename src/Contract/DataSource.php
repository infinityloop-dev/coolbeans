<?php

declare(strict_types = 1);

namespace CoolBeans\Contract;

use CoolBeans\Contract\PrimaryKey;

interface DataSource
{
    /**
     * Returns table name.
     */
    public function getName() : string;

    /**
     * Returns row from table by its id.
     */
    public function getRow(PrimaryKey $key) : \CoolBeans\Contract\Row;

    /**
     * Returns selection of all entries.
     */
    public function findAll() : \CoolBeans\Contract\Selection;

    /**
     * Returns selection of entries found by associative array.
     */
    public function findByArray(array $filter) : \CoolBeans\Contract\Selection;

    /**
     * Inserts data into table.
     */
    public function insert(array $data) : \CoolBeans\Result\Insert;

    /**
     * Inserts multiple rows into table.
     * Returns array of new Primary keys.
     */
    public function insertMultiple(array $data) : \CoolBeans\Result\InsertMultiple;

    /**
     * Updates row.
     */
    public function update(PrimaryKey $key, array $data) : \CoolBeans\Result\Update;

    /**
     * Updates selection of entries found by associative array.
     * Returns number of affected rows.
     */
    public function updateByArray(array $filter, array $data) : \CoolBeans\Result\UpdateByArray;

    /**
     * Deletes row
     */
    public function delete(PrimaryKey $key) : \CoolBeans\Result\Delete;

    /**
     * Deletes selection of entries found by associative array.
     * Returns number of affected rows.
     */
    public function deleteByArray(array $filter) : \CoolBeans\Result\DeleteByArray;

    /**
     * Inserts data if no key provided, updates otherwise.
     */
    public function upsert(?PrimaryKey $key, array $values) : \CoolBeans\Contract\Result;

    /**
     * Executes function enclosed in PDO transaction.
     */
    public function transaction(callable $function);
}
