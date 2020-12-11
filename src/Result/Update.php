<?php

declare(strict_types = 1);

namespace CoolBeans\Result;

use \CoolBeans\Contract\PrimaryKey;

class Update implements \CoolBeans\Contract\Result
{
    use \Nette\SmartObject;

    public PrimaryKey $updatedId;
    public bool $dataChanged;

    public function __construct(PrimaryKey $updatedId, bool $dataChanged)
    {
        $this->updatedId = $updatedId;
        $this->dataChanged = $dataChanged;
    }
}
