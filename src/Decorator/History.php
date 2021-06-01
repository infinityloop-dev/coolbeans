<?php

declare(strict_types = 1);

namespace CoolBeans\Decorator;

final class History implements \CoolBeans\Contract\DataSource
{
    use \Nette\SmartObject;
    use \CoolBeans\Decorator\TDecorator;

    public const METADATA = ['id', 'active'];

    public function __construct(
        \CoolBeans\Contract\DataSource $dataSource,
        private \CoolBeans\Contract\DataSource $historyDataSource,
        private array $additionalData = [],
        private array $metadata = self::METADATA,
    )
    {
        $this->dataSource = $dataSource;
    }

    public function update(\CoolBeans\Contract\PrimaryKey $key, array $data) : \CoolBeans\Result\Update
    {
        if ($this->isMetadataChange($data)) {
            return $this->dataSource->update($key, $data);
        }

        $row = $this->dataSource->getRow($key);
        $historyData = $row->toArray();

        $result = $this->dataSource->update($key, $data);

        if (!$result->dataChanged) {
            return new \CoolBeans\Result\HistoryUpdate($key, false);
        }

        return new \CoolBeans\Result\HistoryUpdate($key, true, $this->insertHistory($key, $historyData));
    }

    public function updateByArray(array $filter, array $data) : \CoolBeans\Result\UpdateByArray
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

        return new \CoolBeans\Result\HistoryUpdateByArray($updatedIds, $changedIds, $historyIds);
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

    private function insertHistory(\CoolBeans\Contract\PrimaryKey $currentKey, array $oldData) : \CoolBeans\Contract\PrimaryKey
    {
        $oldData[$this->getName() . '_id'] = $currentKey->getValue();

        foreach ($this->metadata as $column) {
            unset($oldData[$column]);
        }

        foreach ($this->additionalData as $column => $value) {
            $oldData[$column] = $value;
        }

        return $this->historyDataSource->insert($oldData)->insertedId;
    }
}
