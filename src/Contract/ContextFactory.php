<?php

declare(strict_types = 1);

namespace CoolBeans\Contract;

interface ContextFactory
{
    public function create() : \Nette\Database\Context;
}
