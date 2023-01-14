<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class PrimaryKey
{
    public array $columns;

    public function __construct(
        string... $columns,
    )
    {
        $this->columns = $columns;
    }
}
