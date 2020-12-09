<?php

declare(strict_types = 1);

namespace CoolBeans\Command;

class SqlGeneratorCommand extends \Symfony\Component\Console\Command\Command
{
    private const INDENTATION = '    ';
    public static $defaultName = 'sqlGenerator';

    public function __construct()
    {
        parent::__construct(self::$defaultName);
    }

    public function generateSqlForBean(string $className) : string
    {
        $bean = new \ReflectionClass($className);

        $toReturn = 'CREATE TABLE `' . \Infinityloop\Utils\CaseConverter::toSnakeCase($bean->getShortName()) . '`(' . \PHP_EOL;
        $foreignKeys = '';
        $data = [];

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

            $foreignKeys .= $this->getForeignKey($property);
        }

        $toReturn .= $this->buildTable($data);

        $toReturn .= ($foreignKeys === '' ? '' : \PHP_EOL) . $foreignKeys;

        $toReturn .= ')' . \PHP_EOL;
        $toReturn .= self::INDENTATION . 'CHARSET = `utf8mb4`' . \PHP_EOL;
        $toReturn .= self::INDENTATION . 'COLLATE `utf8mb4_general_ci`;';

        return $toReturn;
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

    private function buildTable(array $data) : string
    {
        $longestNameLength = $this->getLongestByType($data, 'name');
        $longestDataTypeLength = $this->getLongestByType($data, 'dataType');
        $toReturn = '';

        foreach ($data as $row) {
            $nameLength = \strlen($row['name']);
            $dataTypeLength = \strlen($row['dataType']);
            $toReturn .= self::INDENTATION
                . $row['name'] . \str_repeat(' ', $longestNameLength - $nameLength + 1)
                . $row['dataType'] . \str_repeat(' ', $longestDataTypeLength - $dataTypeLength + 1)
                . $row['notNull']
                . $row['default'];

            $toReturn = \rtrim($toReturn) . ',' . \PHP_EOL;
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

        return \count($typeOverride) === 1
            ? $typeOverride[0]->getArguments()[0]
            : match ($type->getName()) {
                'string', \Infinityloop\Utils\Json::class => 'VARCHAR(255)',
                'int' => 'INT(11)',
                'float' => 'FLOAT(11)',
                'bool' => 'TINYINT(1)',
                \CoolBeans\PrimaryKey\IntPrimaryKey::class => 'INT(11) UNSIGNED',
                'DateTime', \Nette\Utils\DateTime::class => 'DATETIME',
            };
    }

    private function getPropertyName(\ReflectionProperty $property) : string
    {
        return '`' . $property->getName() . '`';
    }

    private function getForeignKey(\ReflectionProperty $property) : string
    {
        $type = $property->getType();
        \assert($type instanceof \ReflectionNamedType);

        if ($type->getName() !== \CoolBeans\PrimaryKey\IntPrimaryKey::class || $property->getName() === 'id') {
            return '';
        }

        $tableName = \str_replace('_id', '', $property->getName());

        return self::INDENTATION . 'FOREIGN KEY (`' . $property->getName() . '`) REFERENCES `' . $tableName . '`(`id`),' . \PHP_EOL;
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

            $beans[] = $class;
        }

        return $beans;
    }
}
