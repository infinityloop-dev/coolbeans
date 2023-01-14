<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
final class CheckConstraint
{
    public function __construct(
        public string $expression,
    )
    {
    }
}
