<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute\Types;

final class MysqlForeignKeyConstraintType
{
    use \Nette\StaticClass;

    public const RESTRICT = 'RESTRICT';
    public const NO_ACTION = 'NO ACTION';
    public const CASCADE = 'CASCADE';
    public const SET_NULL = 'SET NULL';
}
