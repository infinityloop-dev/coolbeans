<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\Contract;

final class PrimaryKeyTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateWithIntKey() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('getPrimary')->with(false)->andReturn('id');

        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(10);
        $activeRowMock->expects('getTable')->withNoArgs()->andReturn($selectionMock);

        $primaryKey = \CoolBeans\Contract\PrimaryKey::create($activeRowMock);

        self::assertInstanceOf(\CoolBeans\PrimaryKey\IntPrimaryKey::class, $primaryKey);
    }

    public function testCreateWithArrayKey() : void
    {
        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => 10]);

        $primaryKey = \CoolBeans\Contract\PrimaryKey::create($activeRowMock);

        self::assertInstanceOf(\CoolBeans\PrimaryKey\ArrayPrimaryKey::class, $primaryKey);
    }

    public function testCreateInvalidKey() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('getName')->withNoArgs()->andReturn('table_name');

        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn('invalid');
        $activeRowMock->expects('getTable')->withNoArgs()->andReturn($selectionMock);

        $this->expectException(\CoolBeans\Exception\MissingPrimaryKey::class);

        \CoolBeans\Contract\PrimaryKey::create($activeRowMock);
    }

    public function testFromSelection() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('getPrimary')->times(3)->with(false)->andReturn('id');

        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(10);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(11);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(12);
        $activeRowMock->expects('getTable')->times(3)->withNoArgs()->andReturn($selectionMock);

        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('rewind')->withNoArgs();
        $selectionMock->expects('valid')->withNoArgs()->andReturnTrue();
        $selectionMock->expects('current')->withNoArgs()->andReturn($activeRowMock);
        $selectionMock->expects('next')->withNoArgs();
        $selectionMock->expects('valid')->withNoArgs()->andReturnTrue();
        $selectionMock->expects('current')->withNoArgs()->andReturn($activeRowMock);
        $selectionMock->expects('next')->withNoArgs();
        $selectionMock->expects('valid')->withNoArgs()->andReturnTrue();
        $selectionMock->expects('current')->withNoArgs()->andReturn($activeRowMock);
        $selectionMock->expects('next')->withNoArgs();
        $selectionMock->expects('valid')->withNoArgs()->andReturnFalse();

        self::assertEquals(
            [
                0 => new \CoolBeans\PrimaryKey\IntPrimaryKey(10),
                1 => new \CoolBeans\PrimaryKey\IntPrimaryKey(11),
                2 => new \CoolBeans\PrimaryKey\IntPrimaryKey(12),
            ],
            \CoolBeans\Contract\PrimaryKey::fromSelection($selectionMock),
        );
    }
}
