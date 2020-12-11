<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute\Defaults;

final class MysqlDefaults
{
    use \Nette\StaticClass;

    public const CURRENT_TIMESTAMP = 'CURRENT_TIMETAMP()';
    public const NOW = 'NOW()';
}
