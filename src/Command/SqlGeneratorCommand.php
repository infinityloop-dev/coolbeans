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

    public function generate(string $source) : string
    {
        $beans = $this->getBeans($source);
        $sorter = new \CoolBeans\Utils\TableSorter($beans);

        $sortedBeans = $sorter->sort();

        $ddl = '';
        $lastBean = \array_key_last($sortedBeans);

        foreach ($sortedBeans as $key => $bean) {
            $ddl .= $this->generateBean($bean);

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

    private function generateBean(string $className) : string
    {
        $bean = new \ReflectionClass($className);
        $this->validateBean($bean);

        $beanName = \Infinityloop\Utils\CaseConverter::toSnakeCase($bean->getShortName());
        $toReturn = 'CREATE TABLE `' . $beanName . '`(' . \PHP_EOL;
        $foreignKeys = [];
        $unique = [];
        $data = [];

        $classUnique = $this->getClassUnique($bean);
        $classIndex = $this->getClassIndex($bean);

        foreach ($bean->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->getType() instanceof \ReflectionNamedType) {
                continue;
            }

            $data[] = [
                'name' => $this->getPropertyName($property),
                'dataType' => $this->getDataType($property),
                'notNull' => $this->getNotNull($property),
                'default' => $this->getDefault($property),
                'comment' => $this->getComment($property),
            ];

            $foreignKey = $this->getForeignKey($property);
            $uniqueConstraint = $this->getUnique($property, $beanName);

            if (\is_string($uniqueConstraint)) {
                $unique[] = $uniqueConstraint;
            }

            if (\is_string($foreignKey)) {
                $foreignKeys[] = $foreignKey;
            }
        }

        $toReturn .= $this->buildTable($data);
        $toReturn .= $this->printSection($classIndex);
        $toReturn .= $this->printSection($classUnique);
        $toReturn .= $this->printSection($unique);
        $toReturn .= $this->printSection($foreignKeys);
        $toReturn .= \PHP_EOL . ')' . \PHP_EOL;
        $toReturn .= self::INDENTATION . $this->getTableCharset($bean) . \PHP_EOL;
        $toReturn .= self::INDENTATION . $this->getTableCollation($bean);
        $toReturn .= $this->getTableComment($bean);
        $toReturn .= ';';

        return $toReturn;
    }

    private function buildTable(array $data) : string
    {
        $longestNameLength = $this->getLongestByType($data, 'name');
        $longestDataTypeLength = $this->getLongestByType($data, 'dataType');
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

    private function getLongestByType(array $data, string $type) : int
    {
        $maxLength = 0;

        foreach ($data as $row) {
            $length = \mb_strlen($row[$type]);

            if ($length > $maxLength) {
                $maxLength = $length;
            }
        }

        return $maxLength;
    }

    private function getComment(\ReflectionProperty $property) : string
    {
        $commentAttribute = $property->getAttributes(\CoolBeans\Attribute\Comment::class);

        if (\count($commentAttribute) === 0) {
            return '';
        }

        return ' COMMENT \'' . $commentAttribute[0]->newInstance()->comment . '\'';
    }

    private function getTableComment(\ReflectionClass $bean) : string
    {
        $commentAttribute = $bean->getAttributes(\CoolBeans\Attribute\Comment::class);

        if (\count($commentAttribute) === 0) {
            return '';
        }

        return \PHP_EOL . self::INDENTATION . 'COMMENT = \'' . $commentAttribute[0]->newInstance()->comment . '\'';
    }

    private function getTableCharset(\ReflectionClass $bean) : string
    {
        $charsetAttribute = $bean->getAttributes(\CoolBeans\Attribute\Charset::class);

        if (\count($charsetAttribute) === 0) {
            return 'CHARSET = `utf8mb4`';
        }

        return 'CHARSET = `' . $charsetAttribute[0]->newInstance()->charset . '`';
    }

    private function getTableCollation(\ReflectionClass $bean) : string
    {
        $collationAttribute = $bean->getAttributes(\CoolBeans\Attribute\Collation::class);

        if (\count($collationAttribute) === 0) {
            return 'COLLATE = `utf8mb4_general_ci`';
        }

        return 'COLLATE = `' . $collationAttribute[0]->newInstance()->collation . '`';
    }

    private function getDefault(\ReflectionProperty $property) : string
    {
        if ($property->getName() === 'id' || $this->hasPrimaryKeyAttribute($property)) {
            return ' AUTO_INCREMENT PRIMARY KEY';
        }

        if (!$property->getType()->isBuiltin()) {
            $instance = new \ReflectionClass($property->getType()->getName());

            if ($instance->isEnum()) {
                $enum = $this->getBuildInEnum($property);

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

        $type = $property->getType();
        \assert($type instanceof \ReflectionNamedType);

        $defaultValueAttribute = $property->getAttributes(\CoolBeans\Attribute\DefaultValue::class);

        if (!$property->hasDefaultValue() && \count($defaultValueAttribute) === 0) {
            return '';
        }

        if (\count($defaultValueAttribute) > 0) {
            return ' DEFAULT ' . $defaultValueAttribute[0]->getArguments()[0]->value;
        }

        $defaultValue = $property->getDefaultValue();

        if ($defaultValue === null) {
            return ' DEFAULT NULL';
        }

        return ' DEFAULT ' . match ($property->getType()->getName()) {
            'bool' => ($defaultValue === true ? '1' : '0'),
            'int', 'float' => $defaultValue,
            \Infinityloop\Utils\Json::class => '\'' . $defaultValue->toString() . '\'',
            \CoolBeans\PrimaryKey\IntPrimaryKey::class => $defaultValue->getValue(),
            'string' => '\'' . $defaultValue . '\'',
            default => throw new \Exception('Unsupported default value.'),
        };
    }

    private function getNotNull(\ReflectionProperty $property) : string
    {
        return $property->getType()->allowsNull() === false
            ? 'NOT NULL'
            : '        ';
    }

    private function isBuiltInEnum(\ReflectionProperty $property) : bool
    {
        if (!$property->getType()->isBuiltin()) {
            $instance = new \ReflectionClass($property->getType()->getName());

            if ($instance->isEnum()) {
                return true;
            }
        }

        return false;
    }

    private function getBuildInEnum(\ReflectionProperty $property) : \ReflectionEnum
    {
        return new \ReflectionEnum($property->getType()->getName());
    }

    private function getDataType(\ReflectionProperty $property) : string
    {
        $type = $property->getType();
        \assert($type instanceof \ReflectionNamedType);

        $typeOverride = $property->getAttributes(\CoolBeans\Attribute\TypeOverride::class);

        if ($this->isBuiltInEnum($property) && \count($typeOverride) === 0) {
            $enum = $this->getBuildInEnum($property);

            if ((string) $enum->getBackingType() === 'string') {
                $options = [];

                foreach ($enum->getCases() as $case) {
                    $options[] = $case->getBackingValue();
                }

                return 'ENUM(\'' . \implode('\',\'', $options) . '\')';
            }

            $longestOption = 0;

            foreach ($enum->getCases() as $case) {
                $length = \mb_strlen((string) $case->getBackingValue());

                if ($length > $longestOption) {
                    $longestOption = $length;
                }
            }

            return 'TINYINT(' . $longestOption . ')';
        }

        return \count($typeOverride) >= 1
            ? $typeOverride[0]->newInstance()->getType()
            : match ($type->getName()) {
                'string' => 'VARCHAR(255)',
                \Infinityloop\Utils\Json::class => 'JSON',
                'int' => 'INT(11)',
                'float' => 'DOUBLE',
                'bool' => 'TINYINT(1)',
                \CoolBeans\PrimaryKey\IntPrimaryKey::class => 'INT(11) UNSIGNED',
                \DateTime::class, \Nette\Utils\DateTime::class => 'DATETIME',
                default => throw new \CoolBeans\Exception\DataTypeNotSupported('Data type ' . $type->getName() . ' is not supported.'),
            };
    }

    private function getPropertyName(\ReflectionProperty $property) : string
    {
        return '`' . $property->getName() . '`';
    }

    private function getClassUnique(\ReflectionClass $bean) : array
    {
        if (\count($bean->getAttributes(\CoolBeans\Attribute\ClassUniqueConstraint::class)) === 0) {
            return [];
        }

        $this->validateClassUniqueConstraintDuplication($bean);

        $declaredColumns = [];

        foreach ($bean->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $declaredColumns[$property->getName()] = true;
        }

        $constrains = [];

        foreach ($bean->getAttributes(\CoolBeans\Attribute\ClassUniqueConstraint::class) as $uniqueColumnAttribute) {
            $uniqueColumns = $uniqueColumnAttribute->newInstance()->columns;

            $columns = [];

            foreach ($uniqueColumns as $uniqueColumn) {
                if (!\array_key_exists($uniqueColumn, $declaredColumns)) {
                    throw new \CoolBeans\Exception\ClassUniqueConstraintUndefinedProperty(
                        'Property with name "' . $uniqueColumn . '" given in ClassUniqueConstraint is not defined.',
                    );
                }

                $columns[] = '`' . $uniqueColumn . '`';
            }

            $constrains[] = self::INDENTATION . 'CONSTRAINT `unique_' . \Infinityloop\Utils\CaseConverter::toSnakeCase($bean->getShortName())
                . '_' . \implode('_', $uniqueColumns) . '` UNIQUE (' . \implode(',', $columns) . ')';
        }

        return $constrains;
    }

    private function getClassIndex(\ReflectionClass $bean) : array
    {
        if (\count($bean->getAttributes(\CoolBeans\Attribute\ClassIndex::class)) === 0) {
            return [];
        }

        $declaredColumns = [];
        $indexes = [];

        foreach ($bean->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $declaredColumns[$property->getName()] = true;

            if (\count($property->getAttributes(\CoolBeans\Attribute\Index::class)) === 0) {
                continue;
            }

            $index = $property->getAttributes(\CoolBeans\Attribute\Index::class)[0]->newInstance();

            $indexes[] = self::INDENTATION . 'INDEX `' . \Infinityloop\Utils\CaseConverter::toSnakeCase($bean->getShortName())
                . '_' . $property->getName() . '_index` (`' . $property->getName() . '`' . ($index->order === null
                    ? ''
                    : ' ' . $index->order->value
                ) . ')';
        }

        foreach ($bean->getAttributes(\CoolBeans\Attribute\ClassIndex::class) as $indexAttribute) {
            $indexColumns = $indexAttribute->newInstance()->columns;
            $indexOrders = $indexAttribute->newInstance()->orders;

            $columns = [];
            $i = 0;

            foreach ($indexColumns as $indexColumn) {
                if (!\array_key_exists($indexColumn, $declaredColumns)) {
                    throw new \CoolBeans\Exception\ClassUniqueConstraintUndefinedProperty(
                        'Property with name "' . $indexColumn . '" given in ClassIndex is not defined.',
                    );
                }

                $columns[] = '`' . $indexColumn . '`' . (isset($indexOrders[$i]) && $indexOrders[$i] !== null
                    ? ' ' . $indexOrders[$i]->value
                    : '');
                $i++;
            }

            $indexes[] = self::INDENTATION . 'INDEX `' . \Infinityloop\Utils\CaseConverter::toSnakeCase($bean->getShortName())
                . '_' . \implode('_', $indexColumns) . '_index` (' . \implode(',', $columns) . ')';
        }

        return $indexes;
    }

    private function validateClassUniqueConstraintDuplication(\ReflectionClass $bean) : void
    {
        $constraints = [];

        foreach ($bean->getAttributes(\CoolBeans\Attribute\ClassUniqueConstraint::class) as $uniqueColumnAttribute) {
            $columns = $uniqueColumnAttribute->newInstance()->columns;
            \sort($columns, \SORT_STRING);

            $constraints[] = $columns;
        }

        foreach ($constraints as $key => $columns) {
            foreach ($constraints as $key2 => $columns2) {
                if ($key !== $key2 && $columns === $columns2) {
                    throw new \CoolBeans\Exception\ClassUniqueConstraintDuplicateColumns(
                        'Found duplicate columns defined in ClassUniqueConstraint attribute.',
                    );
                }
            }
        }
    }

    private function getUnique(\ReflectionProperty $property, string $beanName) : ?string
    {
        return \count($property->getAttributes(\CoolBeans\Attribute\UniqueConstraint::class)) > 0
            ? self::INDENTATION . 'CONSTRAINT `unique_' . $beanName . '_' . $property->getName() . '` UNIQUE (`' . $property->getName() . '`)'
            : null;
    }

    private function printSection(array $data) : string
    {
        if (\count($data) === 0) {
            return '';
        }

        return ',' . \PHP_EOL . \PHP_EOL . \implode(',' . \PHP_EOL, $data);
    }

    private function hasPrimaryKeyAttribute(\ReflectionProperty $property) : bool
    {
        return \count($property->getAttributes(\CoolBeans\Attribute\PrimaryKey::class)) > 0;
    }

    private function validateBean(\ReflectionClass $bean) : void
    {
        $hasPrimaryKey = false;

        foreach ($bean->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->getName() === 'id' || $this->hasPrimaryKeyAttribute($property)) {
                $type = $property->getType();
                \assert($type instanceof \ReflectionNamedType);

                if ($type->getName() !== \CoolBeans\PrimaryKey\IntPrimaryKey::class) {
                    throw new \CoolBeans\Exception\PrimaryKeyWithInvalidType(
                        'Column ' . $property->getName() . ' has incorrect type. Expected \CoolBeans\PrimaryKey\IntPrimaryKey.',
                    );
                }

                if ($hasPrimaryKey) {
                    throw new \CoolBeans\Exception\BeanWithMultiplePrimaryKeys('Bean ' . $bean->getShortName() . ' has multiple foreign keys.');
                }

                $hasPrimaryKey = true;
            }
        }
    }

    private function getForeignKey(\ReflectionProperty $property) : ?string
    {
        $type = $property->getType();
        \assert($type instanceof \ReflectionNamedType);

        if (
            $type->getName() !== \CoolBeans\PrimaryKey\IntPrimaryKey::class
            || ($property->getName() === 'id' || $this->hasPrimaryKeyAttribute($property))
        ) {
            return null;
        }

        $foreignKeyAttribute = $property->getAttributes(\CoolBeans\Attribute\ForeignKey::class);
        $foreignKeyConstraintAttribute = $property->getAttributes(\CoolBeans\Attribute\ForeignKeyConstraint::class);

        $foreignKeyConstraintResult = '';

        if (\count($foreignKeyConstraintAttribute) > 0) {
            $foreignKeyConstraint = $foreignKeyConstraintAttribute[0]->newInstance();

            if ($foreignKeyConstraint->onUpdate !== null) {
                $foreignKeyConstraintResult .= ' ON UPDATE ' . $foreignKeyConstraint->onUpdate->value;
            }

            if ($foreignKeyConstraint->onDelete !== null) {
                $foreignKeyConstraintResult .= ' ON DELETE ' . $foreignKeyConstraint->onDelete->value;
            }
        } else {
            $foreignKeyConstraintResult .= ' ON UPDATE ' . \CoolBeans\Attribute\Types\ForeignKeyConstraintType::getDefault();
            $foreignKeyConstraintResult .= ' ON DELETE ' . \CoolBeans\Attribute\Types\ForeignKeyConstraintType::getDefault();
        }

        if (\count($foreignKeyAttribute) > 0) {
            $foreignKey = $foreignKeyAttribute[0]->newInstance();

            $table = $foreignKey->table;
            $column = $foreignKey->column;
        } else {
            $table = \str_replace('_id', '', $property->getName());
            $column = 'id';
        }

        return self::INDENTATION . 'FOREIGN KEY (`' . $property->getName() . '`) REFERENCES `' . $table . '`(`' . $column . '`)'
            . $foreignKeyConstraintResult;
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
