<?php

declare(strict_types = 1);

namespace CoolBeans\Decorator;

use \CoolBeans\Contract\PrimaryKey;

final class Bean implements \CoolBeans\DataSource
{
    use \Nette\SmartObject;
    use \CoolBeans\Decorator\TCommon;

    private \CoolBeans\Contract\DataSource $dataSource;
    private string $rowClass;
    private string $selectionClass;

    public function __construct(\CoolBeans\Contract\DataSource $dataSource, string $rowClass, string $selectionClass)
    {
        if (!\is_subclass_of($rowClass, \CoolBeans\Bean::class) ||
            !\is_subclass_of($selectionClass, \CoolBeans\Selection::class)) {
            throw new \CoolBeans\Exception\InvalidFunctionParameters('Bean decorator can transform only to Bean instance.');
        }

        $this->dataSource = $dataSource;
        $this->rowClass = $rowClass;
        $this->selectionClass = $selectionClass;
    }

    public function getRow(PrimaryKey $entryId) : \CoolBeans\Bean
    {
        return $this->createRow($this->dataSource->getRow($entryId));
    }

    public function findAll() : \CoolBeans\Selection
    {
        return $this->createSelection($this->dataSource->findAll());
    }

    public function findByArray(array $filter) : \CoolBeans\Selection
    {
        return $this->createSelection($this->dataSource->findByArray($filter));
    }

    /**
     * Function to create according ActiveRow wrapper
     */
    protected function createRow(\CoolBeans\Contract\Row $row) : \CoolBeans\Bean
    {
        return new $this->rowClass($row);
    }

    /**
     * Function to create according Selection wrapper
     */
    protected function createSelection(\CoolBeans\Contract\Selection $sel) : \CoolBeans\Selection
    {
        return new $this->selectionClass($sel);
    }
}
