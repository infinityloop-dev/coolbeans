<?php

declare(strict_types = 1);

namespace CoolBeans\Result;

use CoolBeans\Contract\PrimaryKey;

class HistoryUpdate extends Update
{
    public ?PrimaryKey $historyId;

    public function __construct(PrimaryKey $updatedId, bool $dataChanged, ?PrimaryKey $historyId = null)
    {
        parent::__construct($updatedId, $dataChanged);
        $this->historyId = $historyId;
    }
}
