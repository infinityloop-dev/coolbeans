<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans;

abstract class Selection implements \Iterator, \Countable
{
    use \Nette\SmartObject;

    protected const ROW_CLASS = \Infinityloop\CoolBeans\Bean::class;

    protected \Nette\Database\Table\Selection $selection;
    protected \ReflectionClass $reflection;

    final public function __construct(\Nette\Database\Table\Selection $selection)
    {
        $this->selection = $selection;
        $this->reflection =  new \ReflectionClass(static::class);

         if (\App\Bootstrap::isDebugMode()) {
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
     *
     * @return static
     */
    public function select(string $select) : self
    {
        $this->selection->select($select);

        return $this;
    }

    /**
     * Function to pass where query.
     *
     * @param string|int|array $val
     * @return static
     */
    public function where(string $col, $val) : self
    {
        $this->selection->where($col, $val);

        return $this;
    }

    /**
     * Function to pass where query in single parameter.
     *
     * @param string|array $col
     * @return static
     */
    public function whereOne($col) : self
    {
        $this->selection->where($col);

        return $this;
    }

    /**
     * Function to pass whereOr query.
     *
     * @param array $cond
     * @return static
     */
    public function whereOr(array $cond) : self
    {
        $this->selection->whereOr($cond);

        return $this;
    }

    /**
     * Function to pass group query.
     *
     * @return static
     */
    public function group(string $group) : self
    {
        $this->selection->group($group);

        return $this;
    }

    /**
     * Function to pass order query.
     *
     * @return static
     */
    public function order(string $order) : self
    {
        $this->selection->order($order);

        return $this;
    }

    /**
     * Function to pass limit query.
     *
     * @return static
     */
    public function limit(int $limit, ?int $offset = null) : self
    {
        $this->selection->limit($limit, $offset);

        return $this;
    }

    /**
     * Function to pass alias query.
     *
     * @return static
     */
    public function alias(string $tableChain, string $alias) : self
    {
        $this->selection->alias($tableChain, $alias);

        return $this;
    }

    /**
     * Function to fetch row.
     */
    public function fetch() : ?\Infinityloop\CoolBeans\Bean
    {
        return static::createRow($this->selection->fetch());
    }

    /**
     * Function to fetch all rows.
     *
     * @return array<\Infinityloop\CoolBeans\Bean>
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
        return $this->selection->count('*');
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
    public function key() : int
    {
        return $this->selection->key();
    }

    /**
     * Iterator interface method.
     */
    public function current() : ?\Infinityloop\CoolBeans\Bean
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
    final protected static function createRow(?\Nette\Database\Table\ActiveRow $row) : ?\Infinityloop\CoolBeans\Bean
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
        $tableName = $this->getTableName();
        $className = \substr($this->reflection->getShortName(), 0, -9);
        $sepIndex = \Nette\Utils\Strings::indexOf($tableName, '.');

        if (\is_int($sepIndex)) {
            $tableName = \Nette\Utils\Strings::substring($tableName, $sepIndex + 1);
        }

        if ($tableName !== \Infinityloop\Utils\CaseConverter::toSnakeCase($className)) {
            throw new \Infinityloop\CoolBeans\Exception\InvalidTable('Provided Selection table [' . $tableName . '] doesnt match [' . $className . '].');
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
