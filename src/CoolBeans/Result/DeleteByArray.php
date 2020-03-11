<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Result;

class DeleteByArray implements \Infinityloop\CoolBeans\Contract\Result
{
    use \Nette\SmartObject;

    public array $deletedIds;

    public function __construct(array $deletedIds)
    {
        $this->deletedIds = $deletedIds;
    }
}
