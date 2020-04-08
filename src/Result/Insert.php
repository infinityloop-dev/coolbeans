<?php

declare(strict_types = 1);

namespace CoolBeans\Result;

use \CoolBeans\Contract\PrimaryKey;

class Insert implements \CoolBeans\Contract\Result
{
    use \Nette\SmartObject;

    public PrimaryKey $insertedId;
    
    public function __construct(PrimaryKey $insertedId)
    {
        $this->insertedId = $insertedId;
    }
}
