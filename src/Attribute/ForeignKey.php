<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class ForeignKey
{
    public function __construct(
        public string $table,
        public string $column = 'id',
    )
    {
    }
}
