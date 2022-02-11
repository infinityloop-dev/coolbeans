<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Collation
{
    public function __construct(
        public string $collation,
    )
    {
    }
}
