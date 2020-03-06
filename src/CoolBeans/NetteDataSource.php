<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans;

use Infinityloop\CoolBeans\PrimaryKey\PrimaryKey;

interface NetteDataSource extends \Infinityloop\CoolBeans\DataSource
{
    public function getRow(PrimaryKey $key) : \Nette\Database\Table\ActiveRow;

    public function findAll() : \Nette\Database\Table\Selection;

    public function findByArray(array $filter) : \Nette\Database\Table\Selection;
}
