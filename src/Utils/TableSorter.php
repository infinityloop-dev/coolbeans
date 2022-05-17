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
        \asort($this->beans);

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
            $type = $property->getType();

            if (!$type instanceof \ReflectionNamedType || $type->isBuiltin() || !\str_contains($property->getName(), '_')) {
                continue;
            }

            $typeReflection = new \ReflectionClass($type->getName());

            if (!$typeReflection->isSubclassOf(\CoolBeans\Contract\PrimaryKey::class)) {
                continue;
            }

            $foreignKeyTarget = $this->getForeignKeyDependency($property);

            if ($foreignKeyTarget === null) {
                continue;
            }

            if ($foreignKeyTarget === \Infinityloop\Utils\CaseConverter::toSnakeCase($bean->getShortName())) {
                continue; // self dependency
            }

            $toReturn[] = $foreignKeyTarget;
        }

        return $toReturn;
    }

    private function getForeignKeyDependency(\ReflectionProperty $property) : ?string
    {
        $foreignKeyAttribute = $property->getAttributes(\CoolBeans\Attribute\ForeignKey::class);

        if (\count($foreignKeyAttribute) > 0) {
            $foreignKey = $foreignKeyAttribute[0]->newInstance();

            return $foreignKey->table;
        }

        $parts = \explode('_', $property->getName());
        \array_pop($parts);

        return \implode('_', $parts);
    }
}
