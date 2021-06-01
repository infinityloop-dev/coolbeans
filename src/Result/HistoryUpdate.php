<?php

declare(strict_types = 1);

namespace CoolBeans\Result;

class HistoryUpdate extends \CoolBeans\Result\Update
{
    public function __construct(
        \CoolBeans\Contract\PrimaryKey $updatedId,
        bool $dataChanged,
        public ?\CoolBeans\Contract\PrimaryKey $historyId = null,
    )
    {
        parent::__construct($updatedId, $dataChanged);
    }
}
