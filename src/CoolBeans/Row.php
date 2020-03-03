<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans;

abstract class Row implements \ArrayAccess, \IteratorAggregate
{
    use \Nette\SmartObject;

    public ?\Infinityloop\CoolBeans\PrimaryKey\PrimaryKey $primaryKey;
    protected \Nette\Database\Table\ActiveRow $row;
    protected \ReflectionClass $reflection;

    final public function __construct(\Nette\Database\Table\ActiveRow $row)
    {
        $this->row = $row;
        $this->reflection = new \ReflectionClass(static::class);

        if (\App\Bootstrap::isDebugMode()) {
            $this->validateTableName();
            $this->validateMissingColumns();
        }

        $this->initiateProperties();
    }

    /**
     * Returns table name.
     */
    public function getTableName() : string
    {
        return $this->row->getTable()->getName();
    }

    /**
     * Returns iterator to all columns.
     */
    public function getIterator() : \Traversable
    {
        return $this->row->getIterator();
    }

    /**
     * Returns all columns in array.
     */
    public function toArray() : array
    {
        return $this->row->toArray();
    }

    /**
     * Array access interface method.
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->{$offset};
        }

        throw new \Infinityloop\CoolBeans\Exception\InvalidColumn('Column [' . $offset . '] is not defined.');
    }

    /**
     * Array access interface method.
     */
    public function offsetExists($offset) : bool
    {
        try {
            $property = $this->reflection->getProperty($offset);

            return $property->isPublic();
        } catch (\ReflectionException $e) {
            return false;
        }
    }

    /**
     * Array access interface method.
     */
    public function offsetSet($offset, $value) : void
    {
        throw new \Infinityloop\CoolBeans\Exception\ForbiddenOperation('Cannot set to Row.');
    }

    /**
     * Array access interface method.
     */
    public function offsetUnset($offset) : void
    {
        throw new \Infinityloop\CoolBeans\Exception\ForbiddenOperation('Cannot unset from Row.');
    }

    protected function ref(string $key, ?string $throughColumn = null) : ?\Nette\Database\Table\ActiveRow
    {
        return $this->row->ref($key, $throughColumn);
    }

    protected function related(string $key, ?string $throughColumn = null) : \Nette\Database\Table\GroupedSelection
    {
        return $this->row->related($key, $throughColumn);
    }

    protected function validateTableName() : void
    {
        $tableName = $this->getTableName();
        $className = $this->reflection->getShortName();
        $sepIndex = \Nette\Utils\Strings::indexOf($tableName, '.');

        if (\is_int($sepIndex)) {
            $tableName = \Nette\Utils\Strings::substring($tableName, $sepIndex + 1);
        }

        if ($tableName !== \Infinityloop\Utils\CaseConverter::toSnakeCase($className)) {
            throw new \Infinityloop\CoolBeans\Exception\InvalidTable('Provided ActiveRow table [' . $tableName . '] doesnt match [' . $className . '].');
        }
    }

    protected function validateMissingColumns() : void
    {
        foreach ($this->row->toArray() as $name => $value) {
            if (!$this->offsetExists($name)) {
                throw new \Infinityloop\CoolBeans\Exception\MissingProperty('Property for column [' . $name . '] is not defined.');
            }
        }
    }

    protected function initiateProperties() : void
    {
        $this->primaryKey = \Infinityloop\CoolBeans\PrimaryKey\PrimaryKey::create($this->row);

        foreach ($this->reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();
            $type = $property->getType();
            $value = $this->row[$name];

            if ($name === 'primaryKey') {
                continue;
            }

            if (!$type instanceof \ReflectionType) {
                throw new \Infinityloop\CoolBeans\Exception\MissingType('Property [' . $property->getName() . '] does not have type.');
            }

            switch ($type->getName()) {
                case 'bool':
                    $this->{$name} = \is_bool($value)
                        ? $value
                        : $value === 1;

                    break;
                case \Infinityloop\Utils\Json::class:
                    $this->{$name} = \Infinityloop\Utils\Json::fromString($value);

                    break;
                default:
                    $this->{$name} = $value;

                    break;
            }
        }
    }
}
