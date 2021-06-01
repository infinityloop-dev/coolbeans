<?php

declare(strict_types = 1);

namespace CoolBeans\Result;

class HistoryUpdateByArray extends \CoolBeans\Result\UpdateByArray
{
    public function __construct(
        array $updatedIds,
        array $changedIds,
        public array $historyIds,
    )
    {
        parent::__construct($updatedIds, $changedIds);
    }
}
