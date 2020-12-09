<?php

declare(strict_types = 1);

namespace CoolBeans;

use \CoolBeans\Contract\PrimaryKey;

interface DataSource extends \CoolBeans\Contract\DataSource
{
    public function getRow(PrimaryKey $key) : \CoolBeans\Bean;

    public function findAll() : \CoolBeans\Selection;

    public function findByArray(array $filter) : \CoolBeans\Selection;
}
