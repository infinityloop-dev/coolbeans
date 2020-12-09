<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\Decorator;

final class HistoryTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testUpdate() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(10);
        $primaryKeyInsert = new \CoolBeans\PrimaryKey\IntPrimaryKey(11);

        $updateData = ['entity_id' => 2];
        $historyData = ['id' => 1, 'entity_id' => 1];

        $update = new \CoolBeans\Result\Update($primaryKey, true);
        $insert = new \CoolBeans\Result\Insert($primaryKeyInsert);

        $rowMock = \Mockery::mock(\CoolBeans\Contract\Row::class);
        $rowMock->expects('toArray')->withNoArgs()->andReturn($historyData);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('getRow')->with($primaryKey)->andReturn($rowMock);
        $dataSourceMock->expects('update')->with($primaryKey, $updateData)->andReturn($update);
        $dataSourceMock->expects('getName')->withNoArgs()->andReturn('table_name');

        $historyDataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $historyDataSourceMock->expects('insert')->with(['entity_id' => 1, 'table_name_id' => 10, 'deleted' => 0])->andReturn($insert);

        $historyInstance = new \CoolBeans\Decorator\History($dataSourceMock, $historyDataSourceMock, ['deleted' => 0]);

        $result = $historyInstance->update($primaryKey, $updateData);

        self::assertEquals(new \CoolBeans\Result\HistoryUpdate($primaryKey, true, $primaryKeyInsert), $result);
        self::assertEquals($primaryKey, $result->updatedId);
        self::assertEquals(true, $result->dataChanged);
    }

    public function testUpdateWithoutDataChange() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(10);

        $updateData = ['entity_id' => 2];
        $historyData = ['id' => 1, 'entity_id' => 1];

        $update = new \CoolBeans\Result\Update($primaryKey, false);

        $rowMock = \Mockery::mock(\CoolBeans\Contract\Row::class);
        $rowMock->expects('toArray')->withNoArgs()->andReturn($historyData);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('getRow')->with($primaryKey)->andReturn($rowMock);
        $dataSourceMock->expects('update')->with($primaryKey, $updateData)->andReturn($update);

        $historyDataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);

        $historyInstance = new \CoolBeans\Decorator\History($dataSourceMock, $historyDataSourceMock);

        self::assertEquals(new \CoolBeans\Result\HistoryUpdate($primaryKey, false), $historyInstance->update($primaryKey, $updateData));
    }

    public function testUpdateChangedMetadata() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(10);

        $updateData = ['id' => 2];

        $update = new \CoolBeans\Result\Update($primaryKey, false);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('update')->with($primaryKey, $updateData)->andReturn($update);

        $historyDataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);

        $historyInstance = new \CoolBeans\Decorator\History($dataSourceMock, $historyDataSourceMock);

        self::assertEquals($update, $historyInstance->update($primaryKey, $updateData));
    }

    public function testUpdateByArray() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(10);
        $primaryKeyInsert = new \CoolBeans\PrimaryKey\IntPrimaryKey(11);

        $filter = ['entity_id' => 1];
        $updateData = ['entity_id' => 2];

        $update = new \CoolBeans\Result\Update($primaryKey, true);
        $insert = new \CoolBeans\Result\Insert($primaryKeyInsert);

        $rowMock = \Mockery::mock(\CoolBeans\Bridge\Nette\ActiveRow::class);
        $rowMock->expects('getPrimaryKey')->withNoArgs()->andReturn($primaryKey);
        $rowMock->expects('toArray')->withNoArgs()->andReturn(['entity_id' => 1, 'active' => 1]);

        $selectionMock = \Mockery::mock(\CoolBeans\Bridge\Nette\Selection::class);
        $selectionMock->expects('rewind')->withNoArgs();
        $selectionMock->expects('valid')->withNoArgs()->andReturnTrue();
        $selectionMock->expects('current')->withNoArgs()->andReturn($rowMock);
        $selectionMock->expects('next')->withNoArgs();
        $selectionMock->expects('valid')->withNoArgs()->andReturnFalse();

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('findByArray')->with($filter)->andReturn($selectionMock);
        $dataSourceMock->expects('update')->with($primaryKey, $updateData)->andReturn($update);
        $dataSourceMock->expects('getName')->withNoArgs()->andReturn('table_name');

        $historyDataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $historyDataSourceMock->expects('insert')->with(['entity_id' => 1, 'table_name_id' => 10, 'deleted' => 0])->andReturn($insert);

        $historyInstance = new \CoolBeans\Decorator\History($dataSourceMock, $historyDataSourceMock, ['deleted' => 0]);

        $result = $historyInstance->updateByArray($filter, $updateData);

        self::assertEquals(new \CoolBeans\Result\HistoryUpdateByArray([$primaryKey], [$primaryKey], [$primaryKeyInsert]), $result);
        self::assertEquals([$primaryKey], $result->updatedIds);
        self::assertEquals([$primaryKey], $result->changedIds);
    }

    public function testUpdateByArrayWithoutDataChange() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(10);

        $filter = ['entity_id' => 1];
        $updateData = ['entity_id' => 2];

        $update = new \CoolBeans\Result\Update($primaryKey, false);

        $rowMock = \Mockery::mock(\CoolBeans\Bridge\Nette\ActiveRow::class);
        $rowMock->expects('getPrimaryKey')->withNoArgs()->andReturn($primaryKey);
        $rowMock->expects('toArray')->withNoArgs()->andReturn(['entity_id' => 1, 'active' => 1]);

        $selectionMock = \Mockery::mock(\CoolBeans\Bridge\Nette\Selection::class);
        $selectionMock->expects('rewind')->withNoArgs();
        $selectionMock->expects('valid')->withNoArgs()->andReturnTrue();
        $selectionMock->expects('current')->withNoArgs()->andReturn($rowMock);
        $selectionMock->expects('next')->withNoArgs();
        $selectionMock->expects('valid')->withNoArgs()->andReturnFalse();

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('findByArray')->with($filter)->andReturn($selectionMock);
        $dataSourceMock->expects('update')->with($primaryKey, $updateData)->andReturn($update);

        $historyDataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);

        $historyInstance = new \CoolBeans\Decorator\History($dataSourceMock, $historyDataSourceMock);

        self::assertEquals(new \CoolBeans\Result\HistoryUpdateByArray([$primaryKey], [], []), $historyInstance->updateByArray($filter, $updateData));
    }

    public function testUpdateByArrayChangedMetadata() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(10);

        $filter = ['active' => 1];
        $updateData = ['active' => 0];

        $update = new \CoolBeans\Result\UpdateByArray([$primaryKey], []);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('updateByArray')->with($filter, $updateData)->andReturn($update);

        $historyDataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);

        $historyInstance = new \CoolBeans\Decorator\History($dataSourceMock, $historyDataSourceMock);

        self::assertEquals($update, $historyInstance->updateByArray($filter, $updateData));
    }
}
