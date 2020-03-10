<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Result;

use \Infinityloop\CoolBeans\Contract\PrimaryKey;

class Insert implements \Infinityloop\CoolBeans\Contract\Result
{
    use \Nette\SmartObject;

    public PrimaryKey $insertedId;
    
    public function __construct(PrimaryKey $insertedId)
    {
        $this->insertedId = $insertedId;
    }
}
