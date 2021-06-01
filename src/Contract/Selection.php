<?php

declare(strict_types = 1);

namespace CoolBeans\Contract;

interface Selection extends \Iterator, \Countable
{
    public function getTableName() : string;

    public function fetch() : ?\CoolBeans\Contract\Row;

    public function where(string $column, string|int|array ...$val) : static;

    public function count() : int;

    public function rewind() : void;

    public function valid() : bool;

    public function key() : string|int;

    public function current() : ?\CoolBeans\Contract\Row;

    public function next() : void;
}
