<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Result;

use Infinityloop\CoolBeans\Contract\PrimaryKey;

class Update implements \Infinityloop\CoolBeans\Contract\Result
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
