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
                    if (\in_array($beanName, $dependencyTmp, true)) {
                        $dependencies[$classNameTmp] = \array_diff($dependencies[$classNameTmp], [$beanName]);
                    }
                }
            }

            if (!$tableOutputted) {
                throw new \RuntimeException('Cycle detected');
            }
        }

        return $output;
    }

    private function getForeignKeyTables(\ReflectionClass $bean) : array
    {
        $toReturn = [];

        foreach ($bean->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $reference = \CoolBeans\Command\SqlGeneratorCommand::getForeignKeyReference($property);

            if (!\is_array($reference)) {
                continue;
            }

            [$foreignKeyTarget] = $reference;

            if ($foreignKeyTarget === \Infinityloop\Utils\CaseConverter::toSnakeCase($bean->getShortName())) {
                continue; // self dependency
            }

            $toReturn[] = $foreignKeyTarget;
        }

        return $toReturn;
    }
}
