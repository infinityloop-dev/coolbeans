<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class ClassIndex
{
    public function __construct(
        public array $columns,
        public array $orders,
    )
    {
    }
}
