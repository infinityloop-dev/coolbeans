<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Contract;

interface Selection extends \Iterator, \Countable
{
    public function getTableName() : string;

    public function fetch(); // : ?\Infinityloop\CoolBeans\Contract\Row

    public function where(string $column, ...$value); // : static

    public function count() : int;

    public function rewind() : void;

    public function valid() : bool;

    public function key(); // : string|int

    public function current() : ?\Infinityloop\CoolBeans\Contract\Row;

    public function next() : void;
}
