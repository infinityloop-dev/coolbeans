<?php

declare(strict_types = 1);

namespace CoolBeans\Result;

class Update implements \CoolBeans\Contract\Result
{
    use \Nette\SmartObject;

    public function __construct(
        public \CoolBeans\Contract\PrimaryKey $updatedId,
        public bool $dataChanged,
    )
    {
    }
}
