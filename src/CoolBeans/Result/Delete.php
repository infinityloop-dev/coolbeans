<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Result;

use \Infinityloop\CoolBeans\Contract\PrimaryKey;

class Delete implements \Infinityloop\CoolBeans\Contract\Result
{
    use \Nette\SmartObject;

    public PrimaryKey $deletedId;
    
    public function __construct(PrimaryKey $deletedId)
    {
        $this->deletedId = $deletedId;
    }
}
