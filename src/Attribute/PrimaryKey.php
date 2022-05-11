<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class PrimaryKey
{
    public array $columns;

    public function __construct(string ...$columns)
    {
        if (\count($columns) > 1) {
            throw new \CoolBeans\Exception\PrimaryKeyMultipleColumnsNotImplemented('Multiple column PrimaryKey is not implemented yet.');
        }

        $this->columns = $columns;
    }
}
