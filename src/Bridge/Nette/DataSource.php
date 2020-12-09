<?php

declare(strict_types = 1);

namespace CoolBeans\Bridge\Nette;

use \CoolBeans\Contract\PrimaryKey;

interface DataSource extends \CoolBeans\Contract\DataSource
{
    public function getRow(PrimaryKey $key) : \CoolBeans\Bridge\Nette\ActiveRow;

    public function findAll() : \CoolBeans\Bridge\Nette\Selection;

    public function findByArray(array $filter) : \CoolBeans\Bridge\Nette\Selection;
}
