<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Decorator;

use Infinityloop\CoolBeans\PrimaryKey\PrimaryKey;

class Bean implements \Infinityloop\CoolBeans\DataSource
{
    use \Nette\SmartObject;
    use \Infinityloop\CoolBeans\Decorator\TDecorator;

    protected string $rowClass;
    protected string $selectionClass;

    public function __construct(\Infinityloop\CoolBeans\DataSource $dataSource, string $rowClass, string $selectionClass)
    {
        $this->dataSource = $dataSource;
        $this->rowClass = $rowClass;
        $this->selectionClass = $selectionClass;
    }

    public function getRow(PrimaryKey $entryId) : \Infinityloop\CoolBeans\Row
    {
        return $this->createRow($this->dataSource->getRow($entryId));
    }

    public function findAll() : \Infinityloop\CoolBeans\Selection
    {
        return $this->createSelection($this->dataSource->findAll());
    }

    public function findByArray(array $filter) : \Infinityloop\CoolBeans\Selection
    {
        return $this->createSelection($this->dataSource->findByArray($filter));
    }

    /**
     * Function to create according ActiveRow wrapper
     */
    final protected function createRow(\Nette\Database\Table\ActiveRow $row) : \Infinityloop\CoolBeans\Row
    {
        return new $this->rowClass($row);
    }

    /**
     * Function to create according Selection wrapper
     */
    final protected function createSelection(\Nette\Database\Table\Selection $sel) : \Infinityloop\CoolBeans\Selection
    {
        return new $this->selectionClass($sel);
    }
}
