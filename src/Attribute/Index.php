<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Index
{
    public function __construct(
        public ?string $order = null
    )
    {
    }
}
