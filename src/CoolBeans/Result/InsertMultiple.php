<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Result;

class InsertMultiple implements \Infinityloop\CoolBeans\Contract\Result
{
    use \Nette\SmartObject;

    public array $insertedIds;
}
