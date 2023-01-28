<?php

declare(strict_types = 1);

namespace CoolBeans\Command;

final class SqlGeneratorCommand extends \Symfony\Component\Console\Command\Command
{
    private const INDENTATION = '    ';
    public static $defaultName = 'sqlGenerator';

    public function __construct()
    {
        parent::__construct(self::$defaultName);
    }

    public static function getForeignKeyReference(\ReflectionProperty $property) : ?array
    {
        if (!\str_contains($property->getName(), '_')) {
            return null;
        }

        $type = $property->getType();

        if (!$type instanceof \ReflectionNamedType || $type->isBuiltin()) {
            return null;
        }

        $class = new \ReflectionClass($type->getName());

        if (!$class->isSubclassOf(\CoolBeans\Contract\PrimaryKey::class)) {
            return null;
        }

        $foreignKeyAttribute = $property->getAttributes(\CoolBeans\Attribute\ForeignKey::class);

        if (\count($foreignKeyAttribute) > 0) {
            $foreignKey = $foreignKeyAttribute[0]->newInstance();

            return [$foreignKey->table, $foreignKey->column];
        }

        $parts = \explode('_', $property->getName());
        $column = \array_pop($parts);

        return [\implode('_', $parts), $column];
    }

    public function generate(string $source) : string
    {
        $beans = $this->getBeans($source);
        $sorter = new \CoolBeans\Utils\TableSorter($beans);

        $sortedBeans = $sorter->sort();

        $ddl = '';
        $lastBean = \array_key_last($sortedBeans);

        foreach ($sortedBeans as $key => $bean) {
            $ddl .= self::generateBean($bean);

            if ($lastBean !== $key) {
                $ddl .= \PHP_EOL . \PHP_EOL;
            }
        }

        return $ddl;
    }

    protected function configure() : void
    {
        $this->setName(self::$defaultName);
        $this->setDescription('Converts Beans into SQL.');
        $this->addArgument('source', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Path to folder');
        $this->addArgument('output', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Output file path');
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output,
    ) : int
    {
        $source = $input->getArgument('source');
        $outputFile = $input->getArgument('output');
        $ddl = $this->generate($source);

        if (\is_string($outputFile)) {
            \file_put_contents($outputFile, $ddl);
        } else {
            $output->write($ddl);
        }

        return 0;
    }

    private static function generateBean(string $className) : string
    {
        $bean = new \ReflectionClass($className);
        $beanName = $bean->getShortName();
        $columns = [];
        $indexes = self::getClassIndex($bean);
        $foreignKeyConstraints = [];
        $uniqueConstraints = self::getClassUnique($bean);
        $checkConstraints = self::getClassCheck($bean);

        foreach ($bean->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->getType() instanceof \ReflectionNamedType) {
                continue;
            }

            $columns[] = [
                'name' => '`' . $property->getName() . '`',
                'dataType' => self::getDataType($property),
                'notNull' => $property->getType()->allowsNull() === false
                    ? 'NOT NULL'
                    : '        ',
                'default' => self::getDefault($property, $bean),
                'comment' => self::getComment($property),
            ];

            $foreignKey = self::getForeignKey($property);

            if (\is_string($foreignKey)) {
                $foreignKeyConstraints[] = $foreignKey;
            }

            $uniqueConstraint = self::getUnique($property, $beanName);

            if (\is_string($uniqueConstraint)) {
                $uniqueConstraints[] = $uniqueConstraint;
            }

            $checkConstraint = self::getCheck($property, $beanName);

            if (\count($checkConstraint) > 0) {
                $checkConstraints = \array_merge($checkConstraints, $checkConstraint);
            }

            $index = self::getIndex($property, $beanName);

            if (\count($index) > 0) {
                $indexes = \array_merge($indexes, $index);
            }
        }

        return 'CREATE TABLE `' . \Infinityloop\Utils\CaseConverter::toSnakeCase($beanName) . '`(' . \PHP_EOL
            . self::printColumns($columns)
            . self::printSection($indexes)
            . self::printSection($foreignKeyConstraints)
            . self::printSection($uniqueConstraints)
            . self::printSection($checkConstraints)
            . self::printPrimaryKey($bean)
            . \PHP_EOL . ')'
            . self::getTableCharset($bean)
            . self::getTableCollation($bean)
            . self::getTableComment($bean)
            . ';';
    }

    private static function printColumns(array $data) : string
    {
        $longestNameLength = 0;
        $longestDataTypeLength = 0;

        foreach ($data as $row) {
            $longestNameLength = \max(\mb_strlen($row['name']), $longestNameLength);
            $longestDataTypeLength = \max(\mb_strlen($row['dataType']), $longestDataTypeLength);
        }

        $toReturn = [];

        foreach ($data as $row) {
            $nameLength = \strlen($row['name']);
            $dataTypeLength = \strlen($row['dataType']);
            $toReturn[] = \rtrim(self::INDENTATION
                . $row['name'] . \str_repeat(' ', $longestNameLength - $nameLength + 1)
                . $row['dataType'] . \str_repeat(' ', $longestDataTypeLength - $dataTypeLength + 1)
                . $row['notNull']
                . $row['default']
                . $row['comment']);
        }

        return \implode(',' . \PHP_EOL, $toReturn);
    }

    private static function getComment(\ReflectionProperty $property) : string
    {
        $commentAttribute = $property->getAttributes(\CoolBeans\Attribute\Comment::class);

        if (\count($commentAttribute) === 0) {
            return '';
        }

        return ' COMMENT \'' . $commentAttribute[0]->newInstance()->comment . '\'';
    }

    private static function getTableComment(\ReflectionClass $bean) : string
    {
        $commentAttribute = $bean->getAttributes(\CoolBeans\Attribute\Comment::class);

        if (\count($commentAttribute) === 0) {
            return '';
        }

        return \PHP_EOL . self::INDENTATION . 'COMMENT = \'' . $commentAttribute[0]->newInstance()->comment . '\'';
    }

    private static function getTableCharset(\ReflectionClass $bean) : string
    {
        $charsetAttribute = $bean->getAttributes(\CoolBeans\Attribute\Charset::class);
        $charset = \count($charsetAttribute) === 0
            ? \CoolBeans\Attribute\Charset::DEFAULT
            : $charsetAttribute[0]->newInstance()->charset;

        return \PHP_EOL . self::INDENTATION . 'CHARSET = `' . $charset . '`';
    }

    private static function getTableCollation(\ReflectionClass $bean) : string
    {
        $collationAttribute = $bean->getAttributes(\CoolBeans\Attribute\Collation::class);
        $collation = \count($collationAttribute) === 0
            ? \CoolBeans\Attribute\Collation::DEFAULT
            : $collationAttribute[0]->newInstance()->collation;

        return \PHP_EOL . self::INDENTATION . 'COLLATE = `' . $collation . '`';
    }

    private static function getDefault(\ReflectionProperty $property, \ReflectionClass $bean) : string
    {
        $type = $property->getType();
        \assert($type instanceof \ReflectionNamedType);

        $defaultValueAttribute = $property->getAttributes(\CoolBeans\Attribute\DefaultValue::class);

        if (\count($defaultValueAttribute) > 0) {
            return ' DEFAULT ' . $defaultValueAttribute[0]->getArguments()[0]->value;
        }

        if (!$property->hasDefaultValue()) {
            $primaryKeys = self::getPrimaryKeyColumns($bean);

            if (\count($primaryKeys) === 1 &&
                $property->getName() === $primaryKeys[0] &&
                \in_array($type->getName(), ['int', \CoolBeans\PrimaryKey\IntPrimaryKey::class], true)) {
                return ' AUTO_INCREMENT';
            }

            return '';
        }

        $defaultValue = $property->getDefaultValue();

        if ($defaultValue === null) {
            return ' DEFAULT NULL';
        }

        if (!$type->isBuiltin()) {
            $typeReflection = new \ReflectionClass($type->getName());

            if ($typeReflection->isEnum()) {
                $enum = new \ReflectionEnum($type->getName());

                if ((string) $enum->getBackingType() === 'string') {
                    return $property->hasDefaultValue()
                        ? ' DEFAULT \'' . $property->getDefaultValue()->value . '\''
                        : '';
                }

                return $property->hasDefaultValue()
                    ? ' DEFAULT ' . $property->getDefaultValue()->value
                    : '';
            }
        }

        return ' DEFAULT ' . match ($type->getName()) {
            'bool' => ($defaultValue === true ? '1' : '0'),
            'int', 'float' => $defaultValue,
            \Infinityloop\Utils\Json::class => '\'' . $defaultValue->toString() . '\'',
            \CoolBeans\PrimaryKey\IntPrimaryKey::class => $defaultValue->getValue(),
            'string' => '\'' . $defaultValue . '\'',
            default => '',
        };
    }

    private static function getDataType(\ReflectionProperty $property) : string
    {
        $type = $property->getType();
        \assert($type instanceof \ReflectionNamedType);

        $typeOverride = $property->getAttributes(\CoolBeans\Attribute\TypeOverride::class);

        if (\count($typeOverride) > 0) {
            return $typeOverride[0]->newInstance()->getType();
        }

        if (!$type->isBuiltin()) {
            $typeReflection = new \ReflectionClass($type->getName());

            if ($typeReflection->isEnum()) {
                $enum = new \ReflectionEnum($type->getName());

                if ((string) $enum->getBackingType() === 'string') {
                    $cases = $enum->getCases();

                    // save large enums as varchar
                    if (\count($cases) > 10) {
                        $longestOption = 0;

                        foreach ($enum->getCases() as $case) {
                            $longestOption = \max($longestOption, \strlen($case->getBackingValue()));
                        }

                        return 'VARCHAR(' . $longestOption . ')';
                    }

                    $options = [];

                    foreach ($enum->getCases() as $case) {
                        $options[] = $case->getBackingValue();
                    }

                    return 'ENUM(\'' . \implode('\',\'', $options) . '\')';
                }

                // max number of digits in backing of integer
                $longestOption = 0;

                foreach ($enum->getCases() as $case) {
                    $longestOption = \max($longestOption, \strlen((string) $case->getBackingValue()));
                }

                return 'TINYINT(' . $longestOption . ')';
            }
        }
        }

        return match ($type->getName()) {
            'string' => 'VARCHAR(255)',
            'int' => 'INT(11)',
            'float' => 'DOUBLE',
            'bool' => 'TINYINT(1)',
            \Infinityloop\Utils\Json::class => 'JSON',
            \CoolBeans\PrimaryKey\IntPrimaryKey::class => 'INT(11) UNSIGNED',
            \DateTime::class, \Nette\Utils\DateTime::class => 'DATETIME',
            default => throw new \CoolBeans\Exception\DataTypeNotSupported('Data type ' . $type->getName() . ' is not supported.'),
        };
    }

    private static function getIndex(\ReflectionProperty $property, string $beanName) : array
    {
        $return = [];

        foreach ($property->getAttributes(\CoolBeans\Attribute\Index::class) as $i => $attribute) {
            $indexName = 'index_' . $beanName . '_' . $property->getName() . '_' . $i;

            $return[] = self::INDENTATION . 'INDEX `' . $indexName . '` (`' . $property->getName() . '` ' . $attribute->newInstance()->order->value . ')';
        }

        return $return;
    }

    private static function getClassIndex(\ReflectionClass $bean) : array
    {
        $return = [];

        foreach ($bean->getAttributes(\CoolBeans\Attribute\ClassIndex::class) as $i => $attribute) {
            $indexName = 'index_' . $bean->getShortName() . '_' . $i;
            $columns = $attribute->newInstance()->columns;
            self::validateColumnsExists($bean, $columns);
            $indexOrders = $attribute->newInstance()->orders;
            $columnsWithOrder = [];

            foreach ($columns as $j => $indexColumn) {
                $order = \array_key_exists($j, $indexOrders) && $indexOrders[$j] instanceof \CoolBeans\Attribute\Types\Order
                    ? $indexOrders[$j]
                    : \CoolBeans\Attribute\Types\Order::ASC;
                $columnsWithOrder[] = '`' . $indexColumn . '` ' . $order->value;
            }

            $return[] = self::INDENTATION . 'INDEX `' . $indexName .'` (' . \implode(', ', $columnsWithOrder) . ')';
        }

        return $return;
    }

    private static function getUnique(\ReflectionProperty $property, string $beanName) : ?string
    {
        $constraintName = 'unique_' . $beanName . '_' . $property->getName();

        return \count($property->getAttributes(\CoolBeans\Attribute\UniqueConstraint::class)) > 0
            ? self::INDENTATION . 'CONSTRAINT `' . $constraintName . '` UNIQUE (`' . $property->getName() . '`)'
            : null;
    }

    private static function getClassUnique(\ReflectionClass $bean) : array
    {
        $return = [];

        foreach ($bean->getAttributes(\CoolBeans\Attribute\ClassUniqueConstraint::class) as $index => $attribute) {
            $constraintName = 'unique_' . $bean->getShortName() . '_' . $index;
            $columns = $attribute->newInstance()->columns;
            self::validateColumnsExists($bean, $columns);

            $return[] = self::INDENTATION . 'CONSTRAINT `' . $constraintName . '` UNIQUE (' . \implode(', ', self::quoteColumns($columns)) . ')';
        }

        return $return;
    }

    private static function getCheck(\ReflectionProperty $property, string $beanName) : array
    {
        $return = [];

        foreach ($property->getAttributes(\CoolBeans\Attribute\CheckConstraint::class) as $index => $attribute) {
            $constraintName = 'check_' . $beanName . '_' . $property->getName() . '_' . $index;

            $return[] = self::INDENTATION . 'CONSTRAINT `' . $constraintName . '` CHECK (' . $attribute->newInstance()->expression . ')';
        }

        $type = $property->getType();

        if ($type instanceof \ReflectionNamedType &&
            $type->isBuiltin() &&
            $type->getName() === 'string' &&
            \count($property->getAttributes(\CoolBeans\Attribute\AllowEmptyString::class)) === 0) {
            $constraintName = 'check_' . $beanName . '_' . $property->getName() . '_string_not_empty';
            $return[] = self::INDENTATION . 'CONSTRAINT `' . $constraintName . '` CHECK (`' . $property->name . '` != \'\')';
        }

        return $return;
    }

    private static function getClassCheck(\ReflectionClass $bean) : array
    {
        $return = [];

        foreach ($bean->getAttributes(\CoolBeans\Attribute\ClassCheckConstraint::class) as $index => $attribute) {
            $constraintName = 'check_' . $bean->getShortName() . '_' . $index;

            $return[] = self::INDENTATION . 'CONSTRAINT `' . $constraintName . '` CHECK (' . $attribute->newInstance()->expression . ')';
        }

        return $return;
    }

    private static function printSection(array $data) : string
    {
        if (\count($data) === 0) {
            return '';
        }

        return ',' . \PHP_EOL . \PHP_EOL . \implode(',' . \PHP_EOL, $data);
    }

    private static function getForeignKey(\ReflectionProperty $property) : ?string
    {
        $reference = self::getForeignKeyReference($property);

        if (!\is_array($reference)) {
            return null;
        }

        [$table, $column] = $reference;
        $attributes = $property->getAttributes(\CoolBeans\Attribute\ForeignKeyConstraint::class);
        $constraint = '';

        if (\count($attributes) > 0) {
            $foreignKeyConstraint = $attributes[0]->newInstance();

            if ($foreignKeyConstraint->onUpdate !== null) {
                $constraint .= ' ON UPDATE ' . $foreignKeyConstraint->onUpdate->value;
            }

            if ($foreignKeyConstraint->onDelete !== null) {
                $constraint .= ' ON DELETE ' . $foreignKeyConstraint->onDelete->value;
            }
        } else {
            $constraint .= ' ON UPDATE ' . \CoolBeans\Attribute\Types\ForeignKeyConstraintType::getDefault();
            $constraint .= ' ON DELETE ' . \CoolBeans\Attribute\Types\ForeignKeyConstraintType::getDefault();
        }

        return self::INDENTATION . 'FOREIGN KEY (`' . $property->getName() . '`) REFERENCES `' . $table . '`(`' . $column . '`)' . $constraint;
    }

    private static function getPrimaryKeyColumns(\ReflectionClass $bean) : array
    {
        $attributes = $bean->getAttributes(\CoolBeans\Attribute\PrimaryKey::class);

        if (\count($attributes) > 0) {
            $primaryColumns = $attributes[0]->newInstance()->columns;
        } else {
            // default
            $primaryColumns = ['id'];
        }

        self::validateColumnsExists($bean, $primaryColumns);

        return $primaryColumns;
    }

    private static function printPrimaryKey(\ReflectionClass $bean) : string
    {
        $columns = self::getPrimaryKeyColumns($bean);

        return  ',' . \PHP_EOL . \PHP_EOL . self::INDENTATION . 'PRIMARY KEY (' . \implode(', ', self::quoteColumns($columns)) . ')';
    }

    private static function validateColumnsExists(\ReflectionClass $bean, array $columns) : void
    {
        if (\count($columns) === 0) {
            throw new \CoolBeans\Exception\EmptyColumnArray($bean->getShortName());
        }

        if (\count($columns) !== \count(\array_flip($columns))) {
            throw new \CoolBeans\Exception\DuplicateInColumnArray($bean->getShortName());
        }

        foreach ($columns as $column) {
            try {
                $property = $bean->getProperty($column);
            } catch (\ReflectionException) {
                throw new \CoolBeans\Exception\UnknownColumnInColumnArray($column, $bean->getShortName());
            }
        }
    }

    private static function quoteColumns(array $columns) : array
    {
        $quotedColumns = [];

        foreach ($columns as $column) {
            $quotedColumns[] = '`' . $column . '`';
        }

        return $quotedColumns;
    }

    private function getBeans(string $destination) : array
    {
        $robotLoader = new \Nette\Loaders\RobotLoader();
        $robotLoader->addDirectory($destination);
        $robotLoader->rebuild();

        $foundClasses = \array_keys($robotLoader->getIndexedClasses());

        $beans = [];

        foreach ($foundClasses as $class) {
            if (!\is_subclass_of($class, \CoolBeans\Bean::class)) {
                continue;
            }

            $bean = new \ReflectionClass($class);

            if ($bean->isAbstract()) {
                continue;
            }

            if (\count($bean->getProperties(\ReflectionProperty::IS_PUBLIC)) === 0) {
                throw new \CoolBeans\Exception\BeanWithoutPublicProperty('Bean ' . $bean->getShortName() . ' has no public property.');
            }

            $beans[] = $class;
        }

        return $beans;
    }
}
