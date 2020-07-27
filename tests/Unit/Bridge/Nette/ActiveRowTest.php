<?php

declare(strict_types=1);

namespace CoolBeans\Tests\Unit\Bridge\Nette;

final class ActiveRowTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testGetTableName() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('getName')->withNoArgs()->andReturn('table_name');

        $activeRowInstance = new class(['id' => 15], $selectionMock) extends \CoolBeans\Bridge\Nette\ActiveRow {};

        self::assertEquals('table_name', $activeRowInstance->getTableName());
    }

    public function testGetPrimaryKey() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('getPrimary')->twice()->with(false)->andReturn('id');

        $activeRowInstance = new class(['id' => 15], $selectionMock) extends \CoolBeans\Bridge\Nette\ActiveRow {};

        self::assertEquals(new \CoolBeans\PrimaryKey\IntPrimaryKey(15), $activeRowInstance->getPrimaryKey());
    }
}