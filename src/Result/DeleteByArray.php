<?php

declare(strict_types = 1);

namespace CoolBeans\Result;

class DeleteByArray implements \CoolBeans\Contract\Result
{
    use \Nette\SmartObject;

    public array $deletedIds;

    public function __construct(array $deletedIds)
    {
        $this->deletedIds = $deletedIds;
    }
}
