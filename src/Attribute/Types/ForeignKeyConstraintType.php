<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute\Types;

enum ForeignKeyConstraintType : string
{
    case RESTRICT = 'RESTRICT';
    case NO_ACTION = 'NO ACTION';
    case CASCADE = 'CASCADE';
    case SET_NULL = 'SET NULL';

    public static function getDefault() : string
    {
        return self::RESTRICT->value;
    }
}
