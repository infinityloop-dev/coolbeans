<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Decorator;

use Infinityloop\CoolBeans\Contract\PrimaryKey;

final class Bean implements \Infinityloop\CoolBeans\DataSource
{
    use \Nette\SmartObject;
    use \Infinityloop\CoolBeans\Decorator\TCommon;

    protected \Infinityloop\CoolBeans\Contract\DataSource $dataSource;
    protected string $rowClass;
    protected string $selectionClass;

    public function __construct(\Infinityloop\CoolBeans\Contract\DataSource $dataSource, string $rowClass, string $selectionClass)
    {
        if (!\is_subclass_of($rowClass, \Infinityloop\CoolBeans\Bean::class) ||
            !\is_subclass_of($selectionClass, \Infinityloop\CoolBeans\Selection::class)) {
            throw new \Infinityloop\CoolBeans\Exception\InvalidFunctionParameters('Bean decorator can transform only to Bean instance.');
        }

        $this->dataSource = $dataSource;
        $this->rowClass = $rowClass;
        $this->selectionClass = $selectionClass;
    }

    public function getRow(PrimaryKey $entryId) : \Infinityloop\CoolBeans\Bean
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
    protected function createRow(\Infinityloop\CoolBeans\Contract\Row $row) : \Infinityloop\CoolBeans\Bean
    {
        return new $this->rowClass($row);
    }

    /**
     * Function to create according Selection wrapper
     */
    protected function createSelection(\Infinityloop\CoolBeans\Contract\Selection $sel) : \Infinityloop\CoolBeans\Selection
    {
        return new $this->selectionClass($sel);
    }
}
