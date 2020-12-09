<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute\Types;

class MysqlTypes
{
    use \Nette\StaticClass;

    public const TIMESTAMP = 'TIMESTAMP';
    public const DATETIME = 'DATETIME';
    public const DATE = 'DATE';
    public const TIME = 'TIME';
    public const INT = 'INT';
    public const TINYINT = 'TINYINT';
    public const SMALLINT = 'SMALLINT';
    public const MEDIUMINT = 'MEDIUMINT';
    public const BIGINT = 'BIGINT';
    public const FLOAT = 'FLOAT';
    public const DOUBLE = 'DOUBLE';
}
