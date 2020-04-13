<?php

declare(strict_types=1);

namespace CoolBeans\Tests\Unit;

class TestBean extends \CoolBeans\Bean
{
    public function callValidateTableName() : void
    {
        $this->validateTableName();
    }
}