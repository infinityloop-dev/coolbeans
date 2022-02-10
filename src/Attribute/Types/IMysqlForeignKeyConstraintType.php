<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute\Types;

interface IMysqlForeignKeyConstraintType
{
    public static function getDefault() : string;
}
