<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Result;

class UpdateByArray implements \Infinityloop\CoolBeans\Contract\Result
{
    use \Nette\SmartObject;

    public array $updatedIds;
    public array $changedIds;

    public function __construct(array $updatedIds, array $changedIds)
    {
        $this->updatedIds = $updatedIds;
        $this->changedIds = $changedIds;
    }
}
