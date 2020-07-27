<?php

declare(strict_types=1);

namespace CoolBeans\Tests\Unit;

final class SelectionTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function setUp() : void
    {
        \CoolBeans\Config::$validateColumns = false;
        \CoolBeans\Config::$validateTableName = false;
    }

    public function testGetTableName() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('getName')->withNoArgs()->andReturn('table_name');

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        self::assertEquals('table_name', $selectionInstance->getTableName());
    }

    public function testSelect() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('select')->with('*');

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        self::assertInstanceOf(\CoolBeans\Selection::class, $selectionInstance->select('*'));
    }

    public function testWhere() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('where')->with('active', 1);

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        self::assertInstanceOf(\CoolBeans\Selection::class, $selectionInstance->where('active', 1));
    }

    public function testWhereOne() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('where')->with(['active' => 1]);

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        self::assertInstanceOf(\CoolBeans\Selection::class, $selectionInstance->whereOne(['active' => 1]));
    }

    public function testWhereOr() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('whereOr')->with(['active' => 1, 'name' => 'Shrek']);

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        self::assertInstanceOf(\CoolBeans\Selection::class, $selectionInstance->whereOr(['active' => 1, 'name' => 'Shrek']));
    }

    public function testGroup() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('group')->with('active');

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        self::assertInstanceOf(\CoolBeans\Selection::class, $selectionInstance->group('active'));
    }

    public function testOrder() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('order')->with('active');

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        self::assertInstanceOf(\CoolBeans\Selection::class, $selectionInstance->order('active'));
    }

    public function testLimit() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('limit')->with(5, null);

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        self::assertInstanceOf(\CoolBeans\Selection::class, $selectionInstance->limit(5));
    }

    public function testAlias() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('alias')->with('user', 'u');

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        self::assertInstanceOf(\CoolBeans\Selection::class, $selectionInstance->alias('user', 'u'));
    }

    public function testFetch() : void
    {
        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->shouldReceive('getPrimary')->twice()->with(false)->andReturn(['id' => 10]);

        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('fetch')->withNoArgs()->andReturn($activeRowMock);

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {
            protected const ROW_CLASS = TestBean::class;
        };

        self::assertEquals(new TestBean($activeRowMock), $selectionInstance->fetch());
    }

    public function testFetchAll() : void
    {
        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->shouldReceive('getPrimary')->twice()->with(false)->andReturn(['id' => 10]);

        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('fetch')->withNoArgs()->andReturn($activeRowMock);

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {
            protected const ROW_CLASS = TestBean::class;
        };

        self::assertEquals(new TestBean($activeRowMock), $selectionInstance->fetch());
    }

    public function testFetchPairs() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('fetchPairs')->with('id', 'name')->andReturn([1 => 'Shrek']);

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        self::assertEquals([1 => 'Shrek'], $selectionInstance->fetchPairs('id', 'name'));
    }

    public function testCountNullLimit() : void
    {
        $sqlBuilderMock = \Mockery::mock(\Nette\Database\Table\SqlBuilder::class);
        $sqlBuilderMock->expects('getLimit')->withNoArgs()->andReturnNull();

        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('getSqlBuilder')->withNoArgs()->andReturn($sqlBuilderMock);
        $selectionMock->expects('count')->with('*')->andReturn(5);

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        self::assertEquals(5, $selectionInstance->count());
    }

    public function testCountIntLimit() : void
    {
        $sqlBuilderMock = \Mockery::mock(\Nette\Database\Table\SqlBuilder::class);
        $sqlBuilderMock->expects('getLimit')->withNoArgs()->andReturn(1);

        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('getSqlBuilder')->withNoArgs()->andReturn($sqlBuilderMock);
        $selectionMock->expects('count')->withNoArgs()->andReturn(5);

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        self::assertEquals(5, $selectionInstance->count());
    }

    /*public function testClone() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        //$selectionMock->expects('count')->with('*')->andReturn(5);

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        self::assertEquals($selectionInstance, $selectionInstance->clone());
    }*/

    public function testRewind() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('rewind')->withNoArgs();

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        $selectionInstance->rewind();
    }

    public function testValid() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('valid')->withNoArgs()->andReturn(true);

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        self::assertEquals(true, $selectionInstance->valid());
    }

    public function testKey() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('key')->withNoArgs()->andReturn(5);

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        self::assertEquals(5, $selectionInstance->key());
    }

    public function testCurrent() : void
    {
        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->shouldReceive('getPrimary')->twice()->with(false)->andReturn(['id' => 10]);

        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('current')->withNoArgs()->andReturn($activeRowMock);

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {
            protected const ROW_CLASS = TestBean::class;
        };

        self::assertEquals(new TestBean($activeRowMock), $selectionInstance->current());
    }

    public function testCurrentNullSelection() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('current')->withNoArgs()->andReturn(null);

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        self::assertEquals(null, $selectionInstance->current());
    }

    public function testNext() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('next')->withNoArgs();

        $selectionInstance = new class($selectionMock) extends \CoolBeans\Selection {};

        $selectionInstance->next();
    }

    public function testCreateRowVisibility() : void
    {
        self::assertEquals(true, (new \ReflectionMethod(\CoolBeans\Selection::class, 'createRow'))->isProtected());
    }

    public function testValidateTableName() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('getName')->withNoArgs()->andReturn('test');

        $testSelectionInstance = new TestSelection($selectionMock);

        $testSelectionInstance->callValidateTableName();
    }

    public function testValidateTableNameRelatedSyntax() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('getName')->withNoArgs()->andReturn('user.test');

        $testSelectionInstance = new TestSelection($selectionMock);

        $testSelectionInstance->callValidateTableName();
    }

    public function validateTableNameIncorrectNameDataProvider() : array
    {
        return [
            ['TEST'],
            ['Test'],
            ['TesT'],
            ['tests'],
        ];
    }

    /**
     * @dataProvider validateTableNameIncorrectNameDataProvider
     */
    public function testValidateTableNameIncorrectName() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('getName')->withNoArgs()->andReturn('selection_table');

        $testSelectionInstance = new TestSelection($selectionMock);

        $this->expectException(\CoolBeans\Exception\InvalidTable::class);

        $testSelectionInstance->callValidateTableName();
    }
}
