<?php

declare(strict_types = 1);

namespace CoolBeans\Exception;

final class UnknownColumnInColumnArray extends \Exception
{
    public function __construct(string $column, string $beanName)
    {
        parent::__construct('Column [' . $column . '] given in column array doesnt exist in Bean ' . $beanName . '.');
    }
}
