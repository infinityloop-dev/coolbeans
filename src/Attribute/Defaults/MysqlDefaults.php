<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute\Defaults;

class MysqlDefaults
{
    use \Nette\StaticClass;

    const CURRENT_TIMESTAMP = 'CURRENT_TIMETAMP()';
    const NOW = 'NOW()';
}
