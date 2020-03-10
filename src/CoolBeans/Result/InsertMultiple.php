<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Result;

class InsertMultiple
{
    use \Nette\SmartObject;

    public array $insertedIds;
}
