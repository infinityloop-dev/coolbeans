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

    protected function configure() : void
    {
        $this->setName(self::$defaultName);
        $this->setDescription('Converts Beans into SQL.');
        $this->addArgument('source', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Path to folder');
        $this->addArgument('output', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Output file path');
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) : int
    {
        $converted = '';
        $destination = $input->getArgument('source');
        $beans = $this->getBeans($destination);
        \asort($beans);

        $lastBean = \array_key_last($beans);

        foreach ($beans as $key => $bean) {
            $converted .= $this->generateSqlForBean($bean);

            if ($lastBean !== $key) {
                $converted .= \PHP_EOL . \PHP_EOL;
            }
        }

        $outputFile = $input->getArgument('output');

        if (\is_string($outputFile)) {
            \file_put_contents($outputFile, $converted);
        } else {
            $output->write($converted);
        }

        return 0;
    }

    private function generateSqlForBean(string $className) : string
    {
        $bean = new \ReflectionClass($className);

        $beanName = \Infinityloop\Utils\CaseConverter::toSnakeCase($bean->getShortName());
        $toReturn = 'CREATE TABLE `' . $beanName . '`(' . \PHP_EOL;
        $foreignKeys = [];
        $unique = [];
        $data = [];

        $classUnique = $this->getClassUnique($bean);

        foreach ($bean->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->getType() instanceof \ReflectionNamedType) {
                continue;
            }

            $data[] = [
                'name' => $this->getPropertyName($property),
                'dataType' => $this->getDataType($property),
                'notNull' => $this->getNotNull($property),
                'default' => $this->getDefault($property),
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

        $toReturn .= $this->buildTable($data, \count($foreignKeys) === 0 && \count($unique) === 0 && $classUnique === null);
        $toReturn .= $this->printClassUnique($classUnique, \count($foreignKeys) === 0 && \count($unique) === 0);
        $toReturn .= $this->printUnique($unique, \count($foreignKeys) === 0);
        $toReturn .= $this->printForeignKey($foreignKeys);
        $toReturn .= ')' . \PHP_EOL;
        $toReturn .= self::INDENTATION . 'CHARSET = `utf8mb4`' . \PHP_EOL;
        $toReturn .= self::INDENTATION . 'COLLATE `utf8mb4_general_ci`;';

        return $toReturn;
    }

    private function buildTable(array $data, bool $isLast) : string
    {
        $longestNameLength = $this->getLongestByType($data, 'name');
        $longestDataTypeLength = $this->getLongestByType($data, 'dataType');
        $toReturn = '';
        $lastRow = \array_key_last($data);

        foreach ($data as $key => $row) {
            $nameLength = \strlen($row['name']);
            $dataTypeLength = \strlen($row['dataType']);
            $toReturn .= self::INDENTATION
                . $row['name'] . \str_repeat(' ', $longestNameLength - $nameLength + 1)
                . $row['dataType'] . \str_repeat(' ', $longestDataTypeLength - $dataTypeLength + 1)
                . $row['notNull']
                . $row['default'];

            $toReturn = $isLast && $lastRow === $key
                ? \rtrim($toReturn) . \PHP_EOL
                : \rtrim($toReturn) . ',' . \PHP_EOL;
        }

        return $toReturn;
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

    private function getDefault(\ReflectionProperty $property) : string
    {
        if ($property->getName() === 'id') {
            return ' AUTOINCREMENT';
        }

        $type = $property->getType();
        \assert($type instanceof \ReflectionNamedType);

        $defaultValueAttribute = $property->getAttributes(\CoolBeans\Attribute\DefaultValue::class);

        if (!$property->hasDefaultValue() && \count($defaultValueAttribute) === 0) {
            return '';
        }

        if (\count($defaultValueAttribute) === 1) {
            return ' DEFAULT ' . $defaultValueAttribute[0]->getArguments()[0];
        }

        $defaultValue = $property->getDefaultValue();

        if ($defaultValue === null) {
            return ' DEFAULT NULL';
        }

        if ($type->getName() === 'bool') {
            return ' DEFAULT ' . ($defaultValue === true ? '1' : '0');
        }

        return ' DEFAULT "' . $defaultValue . '"';
    }

    private function getNotNull(\ReflectionProperty $property) : string
    {
        return $property->getType()->allowsNull() === false
            ? 'NOT NULL'
            : '        ';
    }

    private function getDataType(\ReflectionProperty $property) : string
    {
        $type = $property->getType();
        \assert($type instanceof \ReflectionNamedType);

        $typeOverride = $property->getAttributes(\CoolBeans\Attribute\TypeOverride::class);

        return \count($typeOverride) >= 1
            ? $typeOverride[0]->newInstance()->getType()
            : match ($type->getName()) {
                'string' => 'VARCHAR(255)',
                \Infinityloop\Utils\Json::class => 'JSON',
                'int' => 'INT(11)',
                'float' => 'DOUBLE(11)',
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

    private function getClassUnique(\ReflectionClass $bean) : ?string
    {
        if (\count($bean->getAttributes(\CoolBeans\Attribute\ClassUniqueConstraint::class)) === 0) {
            return null;
        }

        $this->validateClassUniqueConstraintDuplication($bean);

        $constrains = [];

        foreach ($bean->getAttributes(\CoolBeans\Attribute\ClassUniqueConstraint::class) as $uniqueColumnAttribute) {
            $uniqueColumns = $uniqueColumnAttribute->newInstance()->columns;

            if (\count($uniqueColumns) < 2) {
                throw new \CoolBeans\Exception\InvalidClassUniqueConstraintColumnCount(
                    'ClassUniqueConstraint expects at least two column names',
                );
            }

            $columns = [];

            foreach ($uniqueColumns as $uniqueColumn) {
                $isValid = false;

                foreach ($bean->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                    if ($property->getName() === $uniqueColumn) {
                        $isValid = true;

                        break;
                    }
                }

                if (!$isValid) {
                    throw new \CoolBeans\Exception\ClassUniqueConstraintUndefinedProperty(
                        'Property with name ' . $uniqueColumn . ' given in ClassUniqueConstraint is not defined.',
                    );
                }

                $columns[] = '`' . $uniqueColumn . '`';
            }

            $constrains[] = self::INDENTATION . 'CONSTRAINT `unique_' . \Infinityloop\Utils\CaseConverter::toSnakeCase($bean->getShortName())
                . '_' . \implode('_', $uniqueColumns) . '` UNIQUE (' . \implode(',', $columns) . ')';
        }

        if (\count($constrains) === 1) {
            return $constrains[0];
        }

        return \implode(',' . \PHP_EOL, $constrains);
    }

    private function validateClassUniqueConstraintDuplication(\ReflectionClass $bean) : void
    {
        $toValidate = [];

        foreach ($bean->getAttributes(\CoolBeans\Attribute\ClassUniqueConstraint::class) as $uniqueColumnAttribute) {
            $toValidate[] = $uniqueColumnAttribute->newInstance()->columns;
        }

        foreach ($toValidate as $key => $item) {
            foreach ($toValidate as $key2 => $toCompare) {
                if ($key !== $key2 && $item === $toCompare) {
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

    private function printForeignKey(array $foreignKeys) : string
    {
        if (\count($foreignKeys) === 0) {
            return '';
        }

        if (\count($foreignKeys) === 1) {
            return \PHP_EOL . $foreignKeys[0] . \PHP_EOL;
        }

        return \PHP_EOL . \implode(',' . \PHP_EOL, $foreignKeys);
    }

    private function printClassUnique(?string $unique, bool $isLast) : string
    {
        if ($unique === null) {
            return '';
        }

        return \PHP_EOL . $unique . ($isLast
            ? '' . \PHP_EOL
            : ',' . \PHP_EOL);
    }

    private function printUnique(array $unique, bool $isLast) : string
    {
        if (\count($unique) === 0) {
            return '';
        }

        if (\count($unique) === 1) {
            return \PHP_EOL . $unique[0] . ($isLast
                    ? ''
                    : ','
                ) . \PHP_EOL;
        }

        return \PHP_EOL . \implode(',' . \PHP_EOL, $unique) . ($isLast
            ? ''
            : ',' . \PHP_EOL);
    }

    private function getForeignKey(\ReflectionProperty $property) : ?string
    {
        $type = $property->getType();
        \assert($type instanceof \ReflectionNamedType);

        if ($type->getName() !== \CoolBeans\PrimaryKey\IntPrimaryKey::class || $property->getName() === 'id') {
            return null;
        }

        $tableName = \str_replace('_id', '', $property->getName());

        return self::INDENTATION . 'FOREIGN KEY (`' . $property->getName() . '`) REFERENCES `' . $tableName . '`(`id`)';
    }

    private function getBeans(string $destination) : array
    {
        $robotLoader = new \Nette\Loaders\RobotLoader();
        $robotLoader->addDirectory(__DIR__ . $destination);
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
