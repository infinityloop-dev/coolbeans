<?php

declare(strict_types = 1);

namespace CoolBeans\Result;

class InsertMultiple implements \CoolBeans\Contract\Result
{
    use \Nette\SmartObject;

    public array $insertedIds;

    public function __construct(array $insertedIds)
    {
        $this->insertedIds = $insertedIds;
    }
}
