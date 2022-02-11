<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class ForeignKeyConstraint
{
    public function __construct(
        public ?\CoolBeans\Attribute\Types\ForeignKeyConstraintType $onUpdate = null,
        public ?\CoolBeans\Attribute\Types\ForeignKeyConstraintType $onDelete = null,
    )
    {
    }
}
