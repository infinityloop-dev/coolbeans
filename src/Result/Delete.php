<?php

declare(strict_types = 1);

namespace CoolBeans\Result;

class Delete implements \CoolBeans\Contract\Result
{
    use \Nette\SmartObject;

    public function __construct(
        public \CoolBeans\Contract\PrimaryKey $deletedId,
    )
    {
    }
}
