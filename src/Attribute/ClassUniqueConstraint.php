<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class ClassUniqueConstraint
{
    public function __construct(
        public array $columns,
    )
    {
        if (\count($columns) < 2) {
            throw new \CoolBeans\Exception\InvalidClassUniqueConstraintColumnCount(
                'ClassUniqueConstraint expects at least two column names.',
            );
        }
    }
}
