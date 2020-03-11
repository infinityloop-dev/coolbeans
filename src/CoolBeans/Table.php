<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans;

use Infinityloop\CoolBeans\Contract\PrimaryKey;
use Infinityloop\CoolBeans\Bridge\Nette\ActiveRow;
use Infinityloop\CoolBeans\Bridge\Nette\Selection;

class Table implements \Infinityloop\CoolBeans\Bridge\Nette\DataSource
{
    use \Nette\SmartObject;

    protected string $tableName;
    protected ?\Nette\Database\Context $context = null;
    protected ?\Infinityloop\CoolBeans\Contract\ContextFactory $contextFactory = null;

    public function __construct(
        string $tableName, 
        ?\Nette\Database\Context $context = null, 
        ?\Infinityloop\CoolBeans\Contract\ContextFactory $contextFactory = null
    )
    {
        if ($context === null && $contextFactory === null) {
            throw new \Infinityloop\CoolBeans\Exception\InvalidFunctionParameters('Either context or its factory must be provided.');
        }

        $this->tableName = $tableName;
        $this->context = $context;
        $this->contextFactory = $contextFactory;
    }

    public function getName() : string
    {
        return $this->tableName;
    }

    public function getRow(PrimaryKey $key) : ActiveRow
    {
        $row = $this->findAll()->wherePrimary($key->getValue())->fetch();

        if (!$row instanceof ActiveRow) {
            throw new \Infinityloop\CoolBeans\Exception\RowNotFound('Row with key [' . $key->printValue() . '] not found in table [' . $this->getName() . '].');
        }

        return $row;
    }

    public function findAll() : Selection
    {
        $cache = \array_values((array) $this->getContext())[3];

        return new Selection($this->getContext(), $this->getContext()->getConventions(), $this->getName(), $cache);
    }

    public function findByArray(array $filter) : Selection
    {
        return $this->findAll()->where($filter);
    }

    public function insert(array $data) : \Infinityloop\CoolBeans\Result\Insert
    {
        $row = $this->findAll()->insert($data);

        if (!$row instanceof \Nette\Database\Table\ActiveRow) {
            throw new \Nette\InvalidStateException('Insert has failed.');
        }

        return new \Infinityloop\CoolBeans\Result\Insert(PrimaryKey::create($row));
    }

    public function insertMultiple(array $data) : \Infinityloop\CoolBeans\Result\InsertMultiple
    {
        $insertedIds = [];
        
        foreach ($data as $toInsert) {
            $result = $this->insert($toInsert);
            $insertedIds[] = $result->insertedId;
        }
        
        return new \Infinityloop\CoolBeans\Result\InsertMultiple($insertedIds);
    }

    public function update(PrimaryKey $key, array $data) : \Infinityloop\CoolBeans\Result\Update
    {
        $changed = $this->getRow($key)->update($data);

        return new \Infinityloop\CoolBeans\Result\Update($key, $changed);
    }

    public function updateByArray(array $filter, array $data) : \Infinityloop\CoolBeans\Result\UpdateByArray
    {
        $updatedIds = [];
        $changedIds = [];

        foreach ($this->findByArray($filter) as $row) {
            $key = PrimaryKey::create($row);
            $updatedIds[] = $key;

            $changed = $row->update($data);

            if ($changed) {
                $changedIds[] = $key;
            }
        }

        return new \Infinityloop\CoolBeans\Result\UpdateByArray($updatedIds, $changedIds);
    }

    public function delete(PrimaryKey $key) : \Infinityloop\CoolBeans\Result\Delete
    {
        $this->getRow($key)->delete();

        return new \Infinityloop\CoolBeans\Result\Delete($key);
    }

    public function deleteByArray(array $filter) : \Infinityloop\CoolBeans\Result\DeleteByArray
    {
        $selection = $this->findByArray($filter);
        $deletedIds = PrimaryKey::fromSelection($selection);
        $selection->delete();

        return new \Infinityloop\CoolBeans\Result\DeleteByArray($deletedIds);
    }
    
    public function upsert(?PrimaryKey $key, array $values) : \Infinityloop\CoolBeans\Contract\Result
    {
        if ($key instanceof PrimaryKey) {
            return $this->update($key, $values);
        }

        return $this->insert($values);
    }

    public function transaction(callable $function)
    {
        $inTransaction = $this->getContext()->getConnection()->getPdo()->inTransaction();

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
     * Returns structure of columns in table.
     */
    public function getStructure() : array
    {
        return $this->getContext()->getStructure()->getColumns($this->tableName);
    }

    protected function getContext() : \Nette\Database\Context
    {
        if (!$this->context instanceof \Nette\Database\Context) {
            $this->context = $this->contextFactory->create();
        }

        return $this->context;
    }
}
