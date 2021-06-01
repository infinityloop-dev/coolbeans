<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class UniqueConstraint
{
    public function __construct()
    {
    }
}
