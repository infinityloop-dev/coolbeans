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

    protected function configure() : void
    {
        $this->setName(self::$defaultName);
        $this->setDescription('Converts Beans into SQL.');
        $this->addArgument('destination', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Path to folder');
        $this->addArgument('output', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Output file path');
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) : int
    {
        $converted = '';
        $destination = $input->getArgument('destination');
        $beans = $this->getBeans($destination);

        foreach ($beans as $bean) {
            $converted .= $this->generateSqlForBean($bean);
        }

        $outputFile = $input->getArgument('output');

        if (\is_string($outputFile)) {
            \file_put_contents($outputFile, $converted);
        } else {
            $output->write($converted);
        }

        return 0;
    }

    public function generateSqlForBean($className) : string
    {
        $bean = new \ReflectionClass($className);

        $toReturn = 'CREATE TABLE `' . \Infinityloop\Utils\CaseConverter::toSnakeCase($bean->getShortName()) . '`(' . \PHP_EOL;
        $foreignKeys = '';
        $data = [];

        foreach ($bean->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->getType() instanceof \ReflectionType) {
                continue;
            }

            $data[] = $this->getData($property);
            $foreignKeys .= $this->getForeignKey($property);
        }

        $toReturn .= $this->buildTable($data);

        $toReturn .= ($foreignKeys === '' ? '' : \PHP_EOL) . $foreignKeys;

        $toReturn .= ')' . \PHP_EOL;
        $toReturn .= self::INDENTATION . 'CHARSET = `utf8mb4`' . \PHP_EOL;
        $toReturn .= self::INDENTATION . 'COLLATE `utf8mb4_general_ci`;' . \PHP_EOL . \PHP_EOL;

        return $toReturn;
    }

    private function buildTable(array $data) : string
    {
        $longestNameLength = $this->getLongestByType($data, 'name');
        $longestDataTypeLength = $this->getLongestByType($data, 'dataType');
        $toReturn = '';

        foreach ($data as $row) {
            $nameLength = \mb_strlen($row['name']);
            $dataTypeLength = \mb_strlen($row['dataType']);
            $toReturn .= self::INDENTATION . $row['name'] . \str_repeat(' ', $longestNameLength - $nameLength + 1);
            $toReturn .= $this->buildDataType($row, $longestDataTypeLength, $dataTypeLength);
            $toReturn .= $row['notNull'] === true ? ' NOT NULL' : '';
            $toReturn .= $row['default'];
            $toReturn .= $this->buildAutoIncrement($row['name']);
            $toReturn .= ',' . \PHP_EOL;
        }

        return $toReturn;
    }

    private function buildAutoIncrement(string $name) : string
    {
        return $name === '`id`' ? ' AUTOINCREMENT' : '';
    }

    private function buildDataType(array $row, int $longestDataTypeLenght, int $dataTypeLenght) : string
    {
        if ($row['notNull'] === false && $row['default'] === '') {
            return $row['dataType'];
        }

        if ($row['notNull'] === false && $row['default'] !== '') {
            return $row['dataType'] . \str_repeat(' ', $longestDataTypeLenght - $dataTypeLenght + 9);
        }

        return $row['dataType'] . \str_repeat(' ', $longestDataTypeLenght - $dataTypeLenght);
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

    private function getData(\ReflectionProperty $property) : array
    {
        return [
            'name' => $this->getPropertyName($property),
            'dataType' => $this->getDataType($property),
            'notNull' => $this->isNotNull($property),
            'default' => $this->getDefault($property),
        ];
    }

    private function getDefault(\ReflectionProperty $property) : string
    {
        $type = $property->getType();
        $defaultValueAttribute = $property->getAttributes(\CoolBeans\Attribute\FunctionDefaultValue::class);

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

    private function isNotNull(\ReflectionProperty $property) : bool
    {
        return !$property->getType()->allowsNull();
    }

    private function getDataType(\ReflectionProperty $property) : string
    {
        $type = $property->getType();
        $typeOverride = $property->getAttributes(\CoolBeans\Attribute\TypeOverride::class);

        if (\count($typeOverride) === 1) {
            $dataType = $typeOverride[0]->getArguments()[0];
        } else {
            $dataType = match ($type->getName()) {
                'string', \Infinityloop\Utils\Json::class => 'VARCHAR(255)',
                'int' => 'INT(11)',
                'float' => 'FLOAT(11)',
                'bool' => 'TINYINT(1)',
                \CoolBeans\PrimaryKey\IntPrimaryKey::class => 'INT(11) UNSIGNED',
                'DateTime', \Nette\Utils\DateTime::class => 'DATETIME',
            };
        }

        return $dataType;
    }

    private function getPropertyName(\ReflectionProperty $property) : string
    {
        return '`' . $property->getName() . '`';
    }

    private function getForeignKey(\ReflectionProperty $property) : string
    {
        if ($property->getType()->getName() !== \CoolBeans\PrimaryKey\IntPrimaryKey::class || $property->getName() === 'id') {
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

        $foundClasses = array_keys($robotLoader->getIndexedClasses());

        $beans = [];

        foreach ($foundClasses as $class) {
            if(!\is_subclass_of($class, \CoolBeans\Bean::class)) {
                continue;
            }

            $beans[] = $class;
        }

        return $beans;
    }
}
