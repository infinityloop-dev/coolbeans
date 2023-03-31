<?php

declare(strict_types = 1);

namespace CoolBeans;

abstract class Bean implements \CoolBeans\Contract\Row, \IteratorAggregate
{
    use \Nette\SmartObject;

    protected \ReflectionClass $reflection;
    protected \CoolBeans\Contract\PrimaryKey $primaryKey;

    final public function __construct(
        protected \Nette\Database\Table\ActiveRow $row,
        private bool $extraColumns = false,
    )
    {
        $this->reflection = new \ReflectionClass(static::class);
        $this->primaryKey = \CoolBeans\Contract\PrimaryKey::create($this->row);

        if (\CoolBeans\Config::$validateColumns && !$this->extraColumns) {
            $this->validateMissingProperties();
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
     * Reload row from database.
     */
    public function refresh() : void
    {
        $this->row = $this->row->getTable()->createSelectionInstance($this->getTableName())->get($this->primaryKey->getValue());
        $this->initiateProperties();
    }

    /**
     * Returns internal Nette row.
     */
    public function getInternalRow() : \Nette\Database\Table\ActiveRow
    {
        return $this->row;
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
    private function validateTableName() : void
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
    private function validateMissingProperties() : void
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
    private function initiateProperties() : void
    {
        foreach ($this->reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $type = $property->getType();

            if (!$type instanceof \ReflectionNamedType) {
                throw new \CoolBeans\Exception\MissingType('Property [' . $property->getName() . '] does not have type.');
            }

            $name = $property->getName();
            $typeName = $type->getName();
            $value = $this->row[$name];

            if ($value === null) {
                if ($type->allowsNull()) {
                    $this->{$name} = null;

                    continue;
                }

                throw new \CoolBeans\Exception\NonNullableType('Property [' . $property->getName() . '] does not have nullable type.');
            }

            $this->{$name} = match ($typeName) {
                'int', 'string', \Nette\Utils\DateTime::class => $value,
                'float' => (float) $value, // converts decimal to float, lossy conversion - only do this if you know what you are doing
                \Infinityloop\Utils\Json::class => \Infinityloop\Utils\Json::fromString($value),
                \CoolBeans\PrimaryKey\IntPrimaryKey::class => new \CoolBeans\PrimaryKey\IntPrimaryKey($value),
                'bool' => \is_bool($value)
                    ? $value
                    : $value === 1,
                default => \is_subclass_of($typeName, \BackedEnum::class)
                    ? $typeName::from($value)
                    : $value,
            };
        }
    }
}
