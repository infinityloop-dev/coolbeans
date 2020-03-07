<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Bridge\Nette;

use Infinityloop\CoolBeans\Contract\PrimaryKey;

interface DataSource extends \Infinityloop\CoolBeans\Contract\DataSource
{
    public function getRow(PrimaryKey $key) : \Infinityloop\CoolBeans\Bridge\Nette\ActiveRow;

    public function findAll() : \Infinityloop\CoolBeans\Bridge\Nette\Selection;

    public function findByArray(array $filter) : \Infinityloop\CoolBeans\Bridge\Nette\Selection;
}
