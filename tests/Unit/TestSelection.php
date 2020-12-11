<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit;

class TestSelection extends \CoolBeans\Selection
{
    public function callValidateTableName() : void
    {
        $this->validateTableName();
    }
}
