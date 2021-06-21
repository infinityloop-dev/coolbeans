<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class ForeignKeyConstraint
{
    public function __construct(
        public ?string $onUpdate = null,
        public ?string $onDelete = null,
    )
    {
    }
}
