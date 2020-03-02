<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\PrimaryKey;

interface PrimaryKey
{
    public function getValue();
    public function getName() : string;
}
