<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\Bridge\Nette;

final class SelectionTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testGetTableName() : void
    {
        $supplementalDriverMock = \Mockery::mock(\Nette\Database\ISupplementalDriver::class);
        $supplementalDriverMock->expects('delimite')->with('table_name')->andReturn('delimited');

        $structureMock = \Mockery::mock(\Nette\Database\IStructure::class);

        $conventionsMock = \Mockery::mock(\Nette\Database\IConventions::class);
        $conventionsMock->expects('getPrimary')->with('table_name')->andReturn('id');

        $contextMock = \Mockery::mock(\Nette\Database\Context::class);
        $contextMock->expects('getConnection->getSupplementalDriver')->withNoArgs()->andReturn($supplementalDriverMock);
        $contextMock->expects('getConventions')->withNoArgs()->andReturn($conventionsMock);
        $contextMock->expects('getStructure')->withNoArgs()->andReturn($structureMock);

        $selection = new \CoolBeans\Bridge\Nette\Selection($contextMock, $conventionsMock, 'table_name');

        self::assertEquals('table_name', $selection->getTableName());
    }

    public function testCurrent() : void
    {
        $supplementalDriverMock = \Mockery::mock(\Nette\Database\ISupplementalDriver::class);
        $supplementalDriverMock->expects('delimite')->with('table_name')->andReturn('delimited');

        $structureMock = \Mockery::mock(\Nette\Database\IStructure::class);

        $conventionsMock = \Mockery::mock(\Nette\Database\IConventions::class);
        $conventionsMock->expects('getPrimary')->with('table_name')->andReturn('id');

        $contextMock = \Mockery::mock(\Nette\Database\Context::class);
        $contextMock->expects('getConnection->getSupplementalDriver')->withNoArgs()->andReturn($supplementalDriverMock);
        $contextMock->expects('getConventions')->withNoArgs()->andReturn($conventionsMock);
        $contextMock->expects('getStructure')->withNoArgs()->andReturn($structureMock);

        $selection = new \CoolBeans\Bridge\Nette\Selection($contextMock, $conventionsMock, 'table_name');

        self::assertEquals(null, $selection->current());
    }

    public function testCreateRow() : void
    {
        $supplementalDriverMock = \Mockery::mock(\Nette\Database\ISupplementalDriver::class);
        $supplementalDriverMock->expects('delimite')->with('table_name')->andReturn('delimited');

        $structureMock = \Mockery::mock(\Nette\Database\IStructure::class);

        $conventionsMock = \Mockery::mock(\Nette\Database\IConventions::class);
        $conventionsMock->expects('getPrimary')->with('table_name')->andReturn('id');

        $contextMock = \Mockery::mock(\Nette\Database\Context::class);
        $contextMock->expects('getConnection->getSupplementalDriver')->withNoArgs()->andReturn($supplementalDriverMock);
        $contextMock->expects('getConventions')->withNoArgs()->andReturn($conventionsMock);
        $contextMock->expects('getStructure')->withNoArgs()->andReturn($structureMock);

        $selection = new class ($contextMock, $conventionsMock, 'table_name') extends \CoolBeans\Bridge\Nette\Selection {
            public function callCreateRow(array $row) : \CoolBeans\Bridge\Nette\ActiveRow
            {
                return $this->createRow($row);
            }
        };

        self::assertEquals(new \CoolBeans\PrimaryKey\IntPrimaryKey(1), $selection->callCreateRow(['id' => 1, 'active' => 1])->getPrimaryKey());
    }
}
