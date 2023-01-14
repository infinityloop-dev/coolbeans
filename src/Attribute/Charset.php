<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Charset
{
    public const DEFAULT = 'utf8mb4';

    public function __construct(
        public string $charset,
    )
    {
    }
}
