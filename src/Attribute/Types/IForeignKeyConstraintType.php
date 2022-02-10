<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute\Types;

interface IForeignKeyConstraintType
{
    public static function getDefault() : string;
}
