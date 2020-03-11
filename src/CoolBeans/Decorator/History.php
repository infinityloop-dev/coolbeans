<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Decorator;

use Infinityloop\CoolBeans\Contract\PrimaryKey;

final class History implements \Infinityloop\CoolBeans\Contract\DataSource
{
    use \Nette\SmartObject;
    use \Infinityloop\CoolBeans\Decorator\TDecorator;

    public const METADATA = ['id', 'active'];

    protected \Infinityloop\CoolBeans\Contract\DataSource $historyDataSource;
    protected array $additionalData;
    protected array $metadata;

    public function __construct(
        \Infinityloop\CoolBeans\Contract\DataSource $dataSource,
        \Infinityloop\CoolBeans\Contract\DataSource $historyDataSource,
        array $additionalData = [],
        array $metadata = self::METADATA
    )
    {
        $this->dataSource = $dataSource;
        $this->historyDataSource = $historyDataSource;
        $this->additionalData = $additionalData;
        $this->metadata = $metadata;
    }

    public function update(PrimaryKey $key, array $data) : \Infinityloop\CoolBeans\Result\Update
    {
        if ($this->isMetadataChange($data)) {
            return $this->dataSource->update($key, $data);
        }

        $row = $this->dataSource->getRow($key);
        $historyData = $row->toArray();

        $result = $this->dataSource->update($key, $data);

        if (!$result->dataChanged) {
            return new \Infinityloop\CoolBeans\Result\HistoryUpdate($key, false);
        }

        return new \Infinityloop\CoolBeans\Result\HistoryUpdate($key, true, $this->insertHistory($key, $historyData));
    }

    public function updateByArray(array $filter, array $data) : \Infinityloop\CoolBeans\Result\UpdateByArray
    {
        if ($this->isMetadataChange($data)) {
            return $this->dataSource->updateByArray($filter, $data);
        }

        $updatedIds = [];
        $changedIds = [];
        $historyIds = [];

        foreach ($this->findByArray($filter) as $row) {
            $key = $row->getPrimaryKey();
            $historyData = $row->toArray();
            $updatedIds[] = $key;

            $result = $this->dataSource->update($key, $data);

            if (!$result->dataChanged) {
                continue;
            }

            $changedIds[] = $key;
            $historyIds[] = $this->insertHistory($key, $historyData);
        }

        return new \Infinityloop\CoolBeans\Result\HistoryUpdateByArray($updatedIds, $changedIds, $historyIds);
    }

    private function isMetadataChange(array $data) : bool
    {
        foreach ($data as $column => $value) {
            if (!\in_array($column, $this->metadata, true)) {
                return false;
            }
        }

        return true;
    }

    private function insertHistory(PrimaryKey $currentKey, array $oldData) : PrimaryKey
    {
        $oldData[$this->getName() . '_id'] = $currentKey->getValue();

        foreach ($this->metadata as $column) {
            unset($oldData[$column]);
        }

        foreach ($this->additionalData as $column => $value) {
            $oldData[$column] = $value;
        }

        $result = $this->historyDataSource->insert($oldData);

        return $result->insertedId;
    }
}
