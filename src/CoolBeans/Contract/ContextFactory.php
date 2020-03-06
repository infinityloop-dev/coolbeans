<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Contract;

interface ContextFactory
{
    public function create() : \Nette\Database\Context;
}
