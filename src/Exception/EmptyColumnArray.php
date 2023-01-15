<?php

declare(strict_types = 1);

namespace CoolBeans\Exception;

final class EmptyColumnArray extends \Exception
{
    public function __construct(string $beanName)
    {
        parent::__construct('Column array cannot be empty in Bean ' . $beanName . '.');
    }
}
