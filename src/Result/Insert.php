<?php

declare(strict_types = 1);

namespace CoolBeans\Result;

class Insert implements \CoolBeans\Contract\Result
{
    use \Nette\SmartObject;

    public function __construct(
        public \CoolBeans\Contract\PrimaryKey $insertedId,
    )
    {
    }
}
