<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute\Types;

enum DefaultFunction : string
{
    case CURRENT_TIMESTAMP = 'CURRENT_TIMESTAMP()';
    case NOW = 'NOW()';
}
