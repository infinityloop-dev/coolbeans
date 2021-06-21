<?php

declare(strict_types = 1);

namespace CoolBeans\Utils;

final class TableSorter
{
    use \Nette\SmartObject;

    public function __construct(
        private array $beans,
    )
    {
    }

    public function sort() : array
    {
        $dependencies = [];
        $output = [];

        foreach ($this->beans as $className) {
            $bean = new \ReflectionClass($className);
            $dependencies[$className] = $this->getForeignKeyTables($bean);
        }

        while (\count($dependencies) !== 0) {
            $tableOutputted = false;

            foreach ($dependencies as $className => $dependency) {
                if (\count($dependency) > 0) {
                    continue;
                }

                $tableOutputted = true;
                $output[] = $className;

                unset($dependencies[$className]);

                $bean = new \ReflectionClass($className);
                $beanName = \Infinityloop\Utils\CaseConverter::toSnakeCase($bean->getShortName());

                foreach ($dependencies as $classNameTmp => $dependencyTmp) {
                    if (\in_array($beanName, $dependencyTmp)) {
                        $dependencies[$classNameTmp] = \array_diff($dependencies[$classNameTmp], [$beanName]);
                    }
                }
            }

            if (!$tableOutputted) {
                throw new \Exception('Cycle detected');
            }
        }

        return $output;
    }

    private function getForeignKeyTables(\ReflectionClass $bean) : array
    {
        $toReturn = [];

        foreach ($bean->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->getType() instanceof \ReflectionNamedType) {
                continue;
            }

            $type = $property->getType();
            \assert($type instanceof \ReflectionNamedType);

            if ($type->getName() !== \CoolBeans\PrimaryKey\IntPrimaryKey::class || !\str_ends_with($property->getName(), '_id')) {
                continue;
            }

            $foreignKeyAttribute = $property->getAttributes(\CoolBeans\Attribute\ForeignKey::class);

            if (\count($foreignKeyAttribute) > 0) {
                $foreignKey = $foreignKeyAttribute[0]->newInstance();

                if (\strtolower($foreignKey->table) === \Infinityloop\Utils\CaseConverter::toSnakeCase($bean->getShortName())) {
                    continue;
                }

                $toReturn[] = $foreignKey->table;

                continue;
            }

            $tableName = \str_replace('_id', '', $property->getName());

            if ($tableName === \Infinityloop\Utils\CaseConverter::toSnakeCase($bean->getShortName())) {
                continue;
            }

            $toReturn[] = \str_replace('_id', '', $property->getName());
        }

        return $toReturn;
    }
}
