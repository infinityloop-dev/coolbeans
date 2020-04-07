<?php

declare(strict_types=1);

namespace CoolBeans\Tests\Unit\Decorator;

final class ActiveTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testGetRow() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(10);

        $rowMock = \Mockery::mock(\CoolBeans\Contract\Row::class);
        $rowMock->active = 1;

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('getRow')->with($primaryKey)->andReturn($rowMock);

        $activeInstance = new \CoolBeans\Decorator\Active($dataSourceMock);

        self::assertEquals($rowMock, $activeInstance->getRow($primaryKey));
    }

    public function testGetRowDeletedRow() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(10);

        $rowMock = \Mockery::mock(\CoolBeans\Contract\Row::class);
        $rowMock->active = -1;

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('getRow')->with($primaryKey)->andReturn($rowMock);

        $activeInstance = new \CoolBeans\Decorator\Active($dataSourceMock);

        $this->expectException(\CoolBeans\Exception\RowNotFound::class);
        $this->expectExceptionMessage('Row with key [10] was deleted.');

        $activeInstance->getRow($primaryKey);
    }

    public function testFindAll() : void
    {
        $returnSelectionMock = \Mockery::mock(\CoolBeans\Bridge\Nette\Selection::class);
        $findAllSelectionMock = \Mockery::mock(\CoolBeans\Bridge\Nette\Selection::class);
        $findAllSelectionMock->expects('where')->with('table_name.active >= ?', 0)->andReturn($returnSelectionMock);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('findAll')->withNoArgs()->andReturn($findAllSelectionMock);
        $dataSourceMock->expects('getName')->withNoArgs()->andReturn('table_name');

        $activeInstance = new \CoolBeans\Decorator\Active($dataSourceMock);

        self::assertEquals($returnSelectionMock, $activeInstance->findAll());
    }

    public function testFindByArray() : void
    {
        $returnSelectionMock = \Mockery::mock(\CoolBeans\Bridge\Nette\Selection::class);
        $findAllSelectionMock = \Mockery::mock(\CoolBeans\Bridge\Nette\Selection::class);
        $findAllSelectionMock->expects('where')->with('table_name.active >= ?', 0)->andReturn($returnSelectionMock);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('findByArray')->with(['active' => 1])->andReturn($findAllSelectionMock);
        $dataSourceMock->expects('getName')->withNoArgs()->andReturn('table_name');

        $activeInstance = new \CoolBeans\Decorator\Active($dataSourceMock);

        self::assertEquals($returnSelectionMock, $activeInstance->findByArray(['active' => 1]));
    }

    public function testDelete() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(10);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('update')->with($primaryKey, ['active' => -1]);

        $activeInstance = new \CoolBeans\Decorator\Active($dataSourceMock);

        self::assertEquals(new \CoolBeans\Result\Delete($primaryKey), $activeInstance->delete($primaryKey));
    }

    public function testDeleteByArray() : void
    {
        $updateByArray = new \CoolBeans\Result\UpdateByArray([], [1]);
        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('updateByArray')->with(['active' => 0], ['active' => -1])->andReturn($updateByArray);

        $activeInstance = new \CoolBeans\Decorator\Active($dataSourceMock);

        self::assertEquals(new \CoolBeans\Result\DeleteByArray($updateByArray->changedIds), $activeInstance->deleteByArray(['active' => 0]));
    }
}