<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute\Types;

enum ColumnType : string
{
    case TIMESTAMP = 'TIMESTAMP';
    case DATETIME = 'DATETIME';
    case DATE = 'DATE';
    case TIME = 'TIME';
    case YEAR = 'YEAR';
    case BIT = 'BIT';
    case INT = 'INT';
    case TINYINT = 'TINYINT';
    case SMALLINT = 'SMALLINT';
    case MEDIUMINT = 'MEDIUMINT';
    case BIGINT = 'BIGINT';
    case FLOAT = 'FLOAT';
    case DOUBLE = 'DOUBLE';
    case DECIMAL = 'DECIMAL';
    case CHAR = 'CHAR';
    case VARCHAR = 'VARCHAR';
    case BINARY = 'BINARY';
    case VARBINARY = 'VARBINARY';
    case TINYBLOB = 'TINYBLOB';
    case TINYTEXT = 'TINYTEXT';
    case TEXT = 'TEXT';
    case BLOB = 'BLOB';
    case MEDIUMTEXT = 'MEDIUMTEXT';
    case MEDIUMBLOB = 'MEDIUMBLOB';
    case LONGTEXT = 'LONGTEXT';
    case LONGBLOB = 'LONGBLOB';
    case BOOL = 'BOOL';
    case JSON = 'JSON';
}
