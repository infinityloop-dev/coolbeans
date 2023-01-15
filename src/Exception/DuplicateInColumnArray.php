<?php

declare(strict_types = 1);

namespace CoolBeans\Exception;

final class DuplicateInColumnArray extends \Exception
{
    public function __construct(string $beanName)
    {
        parent::__construct('Column array has duplicates in Bean ' . $beanName . '.');
    }
}
