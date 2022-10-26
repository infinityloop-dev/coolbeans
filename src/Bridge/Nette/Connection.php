<?php

declare(strict_types = 1);

namespace CoolBeans\Bridge\Nette;

final class Connection extends \Nette\Database\Connection
{
    public function connect() : void
    {
        try {
            parent::connect();
        } catch (\PDOException $e) {
            if (\str_contains($e->getMessage(), 'reset by peer')) {
                parent::connect(); // retry
            }

            throw $e;
        }
    }
    
    public function query(string $sql, ...$params) : \Nette\Database\ResultSet
    {
        try {
            return parent::query($sql, ...$params);
        } catch (\PDOException $e) {
            if ($e->getCode() === 'HY000' && \str_contains($e->getMessage(), 'gone away')) {
                $this->reconnect();

                return parent::query($sql, ...$params); // retry
            }

            throw $e;
        }
    }
}
