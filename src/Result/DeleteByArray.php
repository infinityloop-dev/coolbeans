<?php

declare(strict_types = 1);

namespace CoolBeans\Result;

class DeleteByArray implements \CoolBeans\Contract\Result
{
    use \Nette\SmartObject;

    public function __construct(
        public array $deletedIds,
    )
    {
    }
}
