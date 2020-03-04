<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans;

use Infinityloop\CoolBeans\PrimaryKey\PrimaryKey;

trait TDataSource
{
    public function upsert(?PrimaryKey $key, array $values) : PrimaryKey
    {
        if ($key instanceof PrimaryKey) {
            $this->update($key, $values);

            return $key;
        }

        return $this->insert($values);
    }
}
