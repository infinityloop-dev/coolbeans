<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute\Defaults;

enum Defaults : string
{
    case CURRENT_TIMESTAMP = 'CURRENT_TIMESTAMP()';
    case NOW = 'NOW()';
}
