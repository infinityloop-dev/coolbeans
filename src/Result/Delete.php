<?php

declare(strict_types = 1);

namespace CoolBeans\Result;

use \CoolBeans\Contract\PrimaryKey;

class Delete implements \CoolBeans\Contract\Result
{
    use \Nette\SmartObject;

    public PrimaryKey $deletedId;

    public function __construct(PrimaryKey $deletedId)
    {
        $this->deletedId = $deletedId;
    }
}
