<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class DefaultValue
{
    public function __construct(
        public string $defaultValue,
    )
    {
    }
}
