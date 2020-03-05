<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans;

use Infinityloop\CoolBeans\PrimaryKey\PrimaryKey;

/**
 * Class Table
 *
 * Low level implementation of DataSource interface, provides a way to interact directly with nette/database classes.
 */
class Table implements \Infinityloop\CoolBeans\DataSource
{
    use \Nette\SmartObject;

    protected \Nette\Database\Context $context;
    protected string $tableName;

    public function __construct(\Nette\Database\Context $context, string $tableName)
    {
        $this->context = $context;
        $this->tableName = $tableName;
    }

    public function getName() : string
    {
        return $this->tableName;
    }

    public function getRow(PrimaryKey $key) : \Nette\Database\Table\ActiveRow
    {
        $row = $this->findAll()->wherePrimary($key->getValue())->fetch();

        if (!$row instanceof \Nette\Database\Table\ActiveRow) {
            throw new \Infinityloop\CoolBeans\Exception\RowNotFound('Row with key [' . $key->printValue() . '] not found in table [' . $this->getName() . '].');
        }

        return $row;
    }

    public function findAll() : \Nette\Database\Table\Selection
    {
        return $this->getContext()->table($this->tableName);
    }

    public function findByArray(array $filter) : \Nette\Database\Table\Selection
    {
        return $this->findAll()->where($filter);
    }

    public function insert(array $data) : PrimaryKey
    {
        $row = $this->findAll()->insert($data);

        if (!$row instanceof \Nette\Database\Table\ActiveRow) {
            throw new \Nette\InvalidStateException('Insert has failed.');
        }

        return PrimaryKey::create($row);
    }

    public function insertMultiple(array $data) : array
    {
        $result = $this->findAll()->insert($data);

        if ($result instanceof \Nette\Database\Table\ActiveRow) {
            return [PrimaryKey::create($result)];
        }

        if (\is_int($result)) {
            // TODO
        }

        throw new \Nette\InvalidStateException('Insert has failed.');
    }

    public function update(PrimaryKey $key, array $data) : PrimaryKey
    {
        $this->getRow($key)->update($data);

        return $key;
    }

    public function updateByArray(array $filter, array $data) : int
    {
        return $this->findByArray($filter)->update($data);
    }

    public function delete(PrimaryKey $key) : void
    {
        $this->findRow($key)->delete();
    }

    public function deleteByArray(array $filter) : int
    {
        return $this->findByArray($filter)->delete();
    }
    
    public function upsert(?PrimaryKey $key, array $values) : PrimaryKey
    {
        if ($key instanceof PrimaryKey) {
            $this->update($key, $values);

            return $key;
        }

        return $this->insert($values);
    }

    /**
     * Returns structure of columns in table.
     */
    public function getStructure() : array
    {
        return $this->getContext()->getStructure()->getColumns($this->tableName);
    }

    /**
     * Executes given function in transaction and returns its output.
     * Transaction is rolled back on exception and exception is thrown again.
     */
    public function transaction(callable $function)
    {
        $inTransaction = !$this->getContext()->getConnection()->getPdo()->inTransaction();

        try {
            if (!$inTransaction) {
                $this->getContext()->beginTransaction();
            }

            $result = $function();

            if (!$inTransaction) {
                $this->getContext()->commit();
            }

            return $result;
        } catch (\Throwable $e) {
            if (!$inTransaction) {
                $this->context->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Internal function to initiate context.
     * Adds option to override this function and initiate context manually (for cases you can't use DI).
     */
    protected function getContext() : \Nette\Database\Context
    {
        return $this->context;
    }
}
