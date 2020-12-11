<?php

declare(strict_types = 1);

namespace CoolBeans\Result;

class Insert implements \CoolBeans\Contract\Result
{
    use \Nette\SmartObject;

    public \CoolBeans\Contract\PrimaryKey $insertedId;

    public function __construct(\CoolBeans\Contract\PrimaryKey $insertedId)
    {
        $this->insertedId = $insertedId;
    }
}
