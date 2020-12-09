<?php

declare(strict_types = 1);

namespace CoolBeans;

class Table implements \CoolBeans\Bridge\Nette\DataSource
{
    use \Nette\SmartObject;

    protected string $tableName;
    protected ?\Nette\Database\Context $context = null;
    protected ?\CoolBeans\Contract\ContextFactory $contextFactory = null;

    public function __construct(
        string $tableName,
        ?\Nette\Database\Context $context = null,
        ?\CoolBeans\Contract\ContextFactory $contextFactory = null
    )
    {
        if ($context === null && $contextFactory === null) {
            throw new \CoolBeans\Exception\InvalidFunctionParameters('Either context or its factory must be provided.');
        }

        $this->tableName = $tableName;
        $this->context = $context;
        $this->contextFactory = $contextFactory;
    }

    public function getName() : string
    {
        return $this->tableName;
    }

    public function getRow(\CoolBeans\Contract\PrimaryKey $key) : \CoolBeans\Bridge\Nette\ActiveRow
    {
        $row = $this->findAll()->wherePrimary($key->getValue())->fetch();

        if (!$row instanceof \CoolBeans\Bridge\Nette\ActiveRow) {
            throw new \CoolBeans\Exception\RowNotFound('Row with key [' . $key->printValue() . '] not found in table [' . $this->getName() . '].');
        }

        return $row;
    }

    public function findAll() : \CoolBeans\Bridge\Nette\Selection
    {
        $cache = \array_values((array) $this->getContext())[3];

        return new \CoolBeans\Bridge\Nette\Selection($this->getContext(), $this->getContext()->getConventions(), $this->getName(), $cache);
    }

    public function findByArray(array $filter) : \CoolBeans\Bridge\Nette\Selection
    {
        return $this->findAll()->where($filter);
    }

    public function insert(array $data) : \CoolBeans\Result\Insert
    {
        $row = $this->findAll()->insert($data);

        if (!$row instanceof \Nette\Database\Table\ActiveRow) {
            throw new \Nette\InvalidStateException('Insert has failed.');
        }

        return new \CoolBeans\Result\Insert(\CoolBeans\Contract\PrimaryKey::create($row));
    }

    public function insertMultiple(array $data) : \CoolBeans\Result\InsertMultiple
    {
        $insertedIds = [];

        foreach ($data as $toInsert) {
            $result = $this->insert($toInsert);
            $insertedIds[] = $result->insertedId;
        }

        return new \CoolBeans\Result\InsertMultiple($insertedIds);
    }

    public function update(\CoolBeans\Contract\PrimaryKey $key, array $data) : \CoolBeans\Result\Update
    {
        $changed = $this->getRow($key)->update($data);

        return new \CoolBeans\Result\Update($key, $changed);
    }

    public function updateByArray(array $filter, array $data) : \CoolBeans\Result\UpdateByArray
    {
        $updatedIds = [];
        $changedIds = [];

        foreach ($this->findByArray($filter) as $row) {
            $key = \CoolBeans\Contract\PrimaryKey::create($row);
            $updatedIds[] = $key;

            $changed = $row->update($data);

            if ($changed) {
                $changedIds[] = $key;
            }
        }

        return new \CoolBeans\Result\UpdateByArray($updatedIds, $changedIds);
    }

    public function delete(\CoolBeans\Contract\PrimaryKey $key) : \CoolBeans\Result\Delete
    {
        $this->getRow($key)->delete();

        return new \CoolBeans\Result\Delete($key);
    }

    public function deleteByArray(array $filter) : \CoolBeans\Result\DeleteByArray
    {
        $selection = $this->findByArray($filter);
        $deletedIds = \CoolBeans\Contract\PrimaryKey::fromSelection($selection);
        $selection->delete();

        return new \CoolBeans\Result\DeleteByArray($deletedIds);
    }

    public function upsert(?\CoolBeans\Contract\PrimaryKey $key, array $values) : \CoolBeans\Contract\Result
    {
        if ($key instanceof \CoolBeans\Contract\PrimaryKey) {
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
