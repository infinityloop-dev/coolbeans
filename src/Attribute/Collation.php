<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Collation
{
    public const DEFAULT = 'utf8mb4_general_ci';

    public function __construct(
        public string $collation,
    )
    {
    }
}
