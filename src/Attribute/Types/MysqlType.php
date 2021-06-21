<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute\Types;

final class MysqlType
{
    use \Nette\StaticClass;

    public const TIMESTAMP = 'TIMESTAMP';
    public const DATETIME = 'DATETIME';
    public const DATE = 'DATE';
    public const TIME = 'TIME';
    public const YEAR = 'YEAR';
    public const BIT = 'BIT';
    public const INT = 'INT';
    public const TINYINT = 'TINYINT';
    public const SMALLINT = 'SMALLINT';
    public const MEDIUMINT = 'MEDIUMINT';
    public const BIGINT = 'BIGINT';
    public const FLOAT = 'FLOAT';
    public const DOUBLE = 'DOUBLE';
    public const DECIMAL = 'DECIMAL';
    public const CHAR = 'CHAR';
    public const VARCHAR = 'VARCHAR';
    public const BINARY = 'BINARY';
    public const VARBINARY = 'VARBINARY';
    public const TINYBLOB = 'TINYBLOB';
    public const TINYTEXT = 'TINYTEXT';
    public const TEXT = 'TEXT';
    public const BLOB = 'BLOB';
    public const MEDIUMTEXT = 'MEDIUMTEXT';
    public const MEDIUMBLOB = 'MEDIUMBLOB';
    public const LONGTEXT = 'LONGTEXT';
    public const LONGBLOB = 'LONGBLOB';
    public const BOOL = 'BOOL';
    public const JSON = 'JSON';
}
