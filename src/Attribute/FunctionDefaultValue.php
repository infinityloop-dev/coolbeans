<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class FunctionDefaultValue
{
    public function __construct(public string $defaultValue)
    {

    }
}
