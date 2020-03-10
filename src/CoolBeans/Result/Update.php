<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Result;

use Infinityloop\CoolBeans\Contract\PrimaryKey;

class Update implements \Infinityloop\CoolBeans\Contract\Result
{
    use \Nette\SmartObject;

    public PrimaryKey $updatedId;
    public bool $dataChanged;
}
