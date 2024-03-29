<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class ClassCheckConstraint
{
    public function __construct(
        public string $expression,
    )
    {
    }
}
