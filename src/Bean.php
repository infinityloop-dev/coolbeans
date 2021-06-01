<?php

declare(strict_types = 1);

namespace CoolBeans;

abstract class Bean implements \CoolBeans\Contract\Row, \IteratorAggregate
{
    use \Nette\SmartObject;

    protected \ReflectionClass $reflection;
    protected \CoolBeans\Contract\PrimaryKey $primaryKey;

    public function __construct(
        protected \Nette\Database\Table\ActiveRow $row,
    )
    {
        $this->reflection = new \ReflectionClass(static::class);
        $this->primaryKey = \CoolBeans\Contract\PrimaryKey::create($this->row);

        if (\CoolBeans\Config::$validateColumns) {
            $this->validateMissingColumns();
        }

        if (\CoolBeans\Config::$validateTableName) {
            $this->validateTableName();
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
     * Returns all columns in array.
     */
    public function toArray() : array
    {
        return $this->row->toArray();
    }

    /**
     * Returns primary key object.
     */
    public function getPrimaryKey() : \CoolBeans\Contract\PrimaryKey
    {
        return $this->primaryKey;
    }

    /**
     * Returns iterator to all columns.
     */
    public function getIterator() : \Traversable
    {
        return $this->row->getIterator();
    }

    /**
     * Array access interface method.
     */
    public function offsetGet($offset) : mixed
    {
        if ($this->offsetExists($offset)) {
            return $this->{$offset};
        }

        throw new \CoolBeans\Exception\InvalidColumn('Column [' . $offset . '] is not defined.');
    }

    /**
     * Array access interface method.
     */
    public function offsetExists($offset) : bool
    {
        try {
            $property = $this->reflection->getProperty($offset);

            return $property->isPublic();
        } catch (\ReflectionException) {
            return false;
        }
    }

    /**
     * Array access interface method.
     */
    public function offsetSet($offset, $value) : void
    {
        throw new \CoolBeans\Exception\ForbiddenOperation('Cannot set to Bean.');
    }

    /**
     * Array access interface method.
     */
    public function offsetUnset($offset) : void
    {
        throw new \CoolBeans\Exception\ForbiddenOperation('Cannot unset from Bean.');
    }

    /**
     * Selects referenced row from $table where <referencedRowPrimary> = $throughColumn
     */
    protected function ref(string $table, ?string $throughColumn = null) : ?\Nette\Database\Table\ActiveRow
    {
        return $this->row->ref($table, $throughColumn);
    }

    /**
     * Selects all related columns from $table where $throughColumn = <currentRowPrimary>
     */
    protected function related(string $table, ?string $throughColumn = null) : \Nette\Database\Table\GroupedSelection
    {
        return $this->row->related($table, $throughColumn);
    }

    /**
     * Validates whether table name matches class name.
     *
     * Foo -> foo
     * FooBar -> foo_bar
     */
    protected function validateTableName() : void
    {
        $tableName = $this->getTableName();
        $className = $this->reflection->getShortName();
        $sepIndex = \Nette\Utils\Strings::indexOf($tableName, '.');

        if (\is_int($sepIndex)) {
            $tableName = \Nette\Utils\Strings::substring($tableName, $sepIndex + 1);
        }

        if ($tableName !== \Infinityloop\Utils\CaseConverter::toSnakeCase($className)) {
            throw new \CoolBeans\Exception\InvalidTable('Provided ActiveRow table [' . $tableName . '] doesnt match [' . $className . '].');
        }
    }

    /**
     * Validates whether every column in database have its column property.
     */
    protected function validateMissingColumns() : void
    {
        foreach ($this->row->toArray() as $name => $value) {
            if (!$this->offsetExists($name)) {
                throw new \CoolBeans\Exception\MissingProperty('Property for column [' . $name . '] is not defined.');
            }
        }
    }

    /**
     * Initiates values into column properties.
     */
    protected function initiateProperties() : void
    {
        foreach ($this->reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();
            $type = $property->getType();
            $value = $this->row[$name];

            if (!$type instanceof \ReflectionNamedType) {
                throw new \CoolBeans\Exception\MissingType('Property [' . $property->getName() . '] does not have type.');
            }

            if ($value === null) {
                if ($type->allowsNull()) {
                    $this->{$name} = null;

                    continue;
                }

                throw new \CoolBeans\Exception\NonNullableType('Property [' . $property->getName() . '] does not have nullable type.');
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
                case \CoolBeans\PrimaryKey\IntPrimaryKey::class:
                    $this->{$name} = new \CoolBeans\PrimaryKey\IntPrimaryKey($value);

                    break;
                default:
                    $this->{$name} = $value;

                    break;
            }
        }
    }
}
