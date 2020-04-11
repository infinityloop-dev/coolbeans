<?php

declare(strict_types = 1);

namespace CoolBeans\Result;

class HistoryUpdateByArray extends UpdateByArray
{
    public array $historyIds;

    public function __construct(array $updatedIds, array $changedIds, array $historyIds)
    {
        parent::__construct($updatedIds, $changedIds);
        $this->historyIds = $historyIds;
    }
}
