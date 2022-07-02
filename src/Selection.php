<?php

declare(strict_types = 1);

namespace CoolBeans;

abstract class Selection implements \CoolBeans\Contract\Selection
{
    use \Nette\SmartObject;

    protected const ROW_CLASS = \CoolBeans\Bean::class;

    final public function __construct(
        protected \Nette\Database\Table\Selection $selection,
    )
    {
        if (\CoolBeans\Config::$validateTableName) {
            $this->validateTableName();
        }
    }

    /**
     * Returns table name.
     */
    public function getTableName() : string
    {
        return $this->selection->getName();
    }

    /**
     * Function to pass select query.
     */
    public function select(string $select) : static
    {
        $this->selection->select($select);

        return $this;
    }

    /**
     * Function to pass where query.
     */
    public function where(string $col, string|int|float|array|\BackedEnum ...$val) : static
    {
        $this->selection->where($col, ...$val);

        return $this;
    }

    /**
     * Function to pass where query in single parameter.
     */
    public function whereOne(array|string $col) : static
    {
        $this->selection->where($col);

        return $this;
    }

    /**
     * Function to pass whereOr query.
     */
    public function whereOr(array $cond) : static
    {
        $this->selection->whereOr($cond);

        return $this;
    }

    /**
     * Function to pass group query.
     */
    public function group(string $group) : static
    {
        $this->selection->group($group);

        return $this;
    }

    /**
     * Function to pass order query.
     */
    public function order(string $order) : static
    {
        $this->selection->order($order);

        return $this;
    }

    /**
     * Function to pass limit query.
     */
    public function limit(int $limit, ?int $offset = null) : static
    {
        $this->selection->limit($limit, $offset);

        return $this;
    }

    /**
     * Function to pass alias query.
     */
    public function alias(string $tableChain, string $alias) : static
    {
        $this->selection->alias($tableChain, $alias);

        return $this;
    }

    /**
     * Function to fetch row.
     */
    public function fetch() : ?\CoolBeans\Bean
    {
        return static::createRow($this->selection->fetch());
    }

    /**
     * Function to fetch all rows.
     *
     * @return array<\CoolBeans\Bean>
     */
    public function fetchAll() : array
    {
        return \iterator_to_array($this);
    }

    /**
     * Function to fetch column pairs.
     */
    public function fetchPairs(?string $col1, string $col2) : array
    {
        return $this->selection->fetchPairs($col1, $col2);
    }

    /**
     * Count rows in selection.
     */
    public function count() : int
    {
        return \is_int($this->selection->getSqlBuilder()->getLimit())
            ? $this->selection->count()
            : $this->selection->count('*');
    }

    /**
     * Clones object - same as: $copy = clone $orig;
     */
    public function clone() : self
    {
        $newSel = clone $this->selection;

        return new static($newSel);
    }

    /**
     * Iterator interface method.
     */
    public function rewind() : void
    {
        $this->selection->rewind();
    }

    /**
     * Iterator interface method.
     */
    public function valid() : bool
    {
        return $this->selection->valid();
    }

    /**
     * Iterator interface method.
     */
    public function key() : int|string
    {
        return $this->selection->key();
    }

    /**
     * Iterator interface method.
     */
    public function current() : ?\CoolBeans\Bean
    {
        $current = $this->selection->current();

        return $current instanceof \Nette\Database\Table\ActiveRow ?
            static::createRow($current) :
            null;
    }

    /**
     * Iterator interface method.
     */
    public function next() : void
    {
        $this->selection->next();
    }

    /**
     * Function to return specific Row class.
     */
    final protected static function createRow(?\Nette\Database\Table\ActiveRow $row) : ?\CoolBeans\Bean
    {
        $rowClassName = static::ROW_CLASS;

        return $row instanceof \Nette\Database\Table\ActiveRow
            ? new $rowClassName($row)
            : null;
    }

    /**
     * Validates whether table name matches class name.
     *
     * FooSelection -> foo
     * FooBarSelection -> foo_bar
     */
    protected function validateTableName() : void
    {
        $reflection = new \ReflectionClass(static::class);
        $tableName = $this->getTableName();
        $className = \substr($reflection->getShortName(), 0, -9);
        $sepIndex = \Nette\Utils\Strings::indexOf($tableName, '.');

        if (\is_int($sepIndex)) {
            $tableName = \Nette\Utils\Strings::substring($tableName, $sepIndex + 1);
        }

        if ($tableName !== \Infinityloop\Utils\CaseConverter::toSnakeCase($className)) {
            throw new \CoolBeans\Exception\InvalidTable('Provided Selection table [' . $tableName . '] doesnt match [' . $className . '].');
        }
    }

    /**
     * Inner selection needs to be cloned too.
     */
    protected function __clone()
    {
        $this->selection = clone $this->selection;
    }
}
