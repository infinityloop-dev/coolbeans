<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Result;

class HistoryUpdateByArray extends UpdateByArray
{
    public array $historyIds;

    public function __construct(array $updatedIds, array $historyIds)
    {
        parent::__construct($updatedIds);
        $this->historyIds = $historyIds;
    }
}
