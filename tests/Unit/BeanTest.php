<?php

declare(strict_types=1);

namespace CoolBeans\Tests\Unit;

final class BeanTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    private $activeRowData = [
        'id' => 1
    ];

    public function setUp() : void
    {
        \CoolBeans\Config::$validateColumns = false;
        \CoolBeans\Config::$validateTableName = false;
    }

    public function testGetTableName() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('getName')->withNoArgs()->andReturn('table_name');

        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);
        $activeRowMock->expects('getTable')->withNoArgs()->andReturn($selectionMock);

        $beanInstance = new class($activeRowMock) extends \CoolBeans\Bean {};

        self::assertEquals('table_name', $beanInstance->getTableName());
    }

    public function testToArray() : void
    {
        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);
        $activeRowMock->expects('toArray')->withNoArgs()->andReturn($this->activeRowData);

        $beanInstance = new class($activeRowMock) extends \CoolBeans\Bean {};
        self::assertEquals($this->activeRowData, $beanInstance->toArray());
    }

    public function testGetPrimaryKey() : void
    {
        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);
        $activeRowMock->expects('offsetGet')->with('id')->once()->andReturn($this->activeRowData['id']);

        $beanInstance = new class($activeRowMock) extends \CoolBeans\Bean {
            public int $id;
        };

        $beanInstance->getPrimaryKey();
    }

    public function testGetIterator() : void
    {
        $iteratorInstance = new class implements \Iterator {
            public function rewind() { return false; }
            public function current() { return false; }
            public function key() { return false; }
            public function next() { return false; }
            public function valid() { return false; }
        };

        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);
        $activeRowMock->expects('getIterator')->withNoArgs()->andReturn($iteratorInstance);

        $beanInstance = new class($activeRowMock) extends \CoolBeans\Bean { };

        $beanInstance->getIterator();
    }

    public function testOffsetGet() : void
    {
        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);
        $activeRowMock->expects('offsetGet')->with('id')->once()->andReturn($this->activeRowData['id']);

        $beanInstance = new class($activeRowMock) extends \CoolBeans\Bean {
            public int $id;
        };
        self::assertEquals($this->activeRowData['id'], $beanInstance->offsetGet('id'));
    }

    public function testOffesetGetWithoutPropertyDefinition() : void
    {
        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);

        $beanInstance = new class($activeRowMock) extends \CoolBeans\Bean { };

        $this->expectException(\CoolBeans\Exception\InvalidColumn::class);
        $this->expectExceptionMessage('Column [id] is not defined.');

        $beanInstance->offsetGet('id');
    }

    public function testOffsetSet() : void
    {
        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);

        $beanInstance = new class($activeRowMock) extends \CoolBeans\Bean { };

        $this->expectException(\CoolBeans\Exception\ForbiddenOperation::class);
        $this->expectExceptionMessage('Cannot set to Bean.');

        $beanInstance->offsetSet('offset', 1);
    }

    public function testOffsetUnset() : void
    {
        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);

        $beanInstance = new class($activeRowMock) extends \CoolBeans\Bean { };

        $this->expectException(\CoolBeans\Exception\ForbiddenOperation::class);
        $this->expectExceptionMessage('Cannot unset from Bean.');

        $beanInstance->offsetUnset('offset');
    }

    public function testRef() : void
    {
        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);
        $activeRowMock->expects('ref')->with('table', null)->andReturnSelf();

        $beanInstance = new class($activeRowMock) extends \CoolBeans\Bean {
            public function callRef(string $table, ?string $throughColumn = null)
            {
                $this->ref($table, $throughColumn);
            }
        };

        $beanInstance->callRef('table');
    }

    public function testRelated() : void
    {
        $groupedSelectionMock = \Mockery::mock(\Nette\Database\Table\GroupedSelection::class);

        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);
        $activeRowMock->expects('related')->with('table', null)->andReturn($groupedSelectionMock);

        $beanInstance = new class($activeRowMock) extends \CoolBeans\Bean {
            public function callRelated(string $table, ?string $throughColumn = null)
            {
                $this->related($table, $throughColumn);
            }
        };

        $beanInstance->callRelated('table');
    }

    public function testInitiateProperties() : void
    {
        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('offsetGet')->with('id')->andReturn($this->activeRowData['id']);
        $activeRowMock->expects('offsetGet')->with('active')->andReturn(true);
        $activeRowMock->expects('offsetGet')->with('ready')->andReturn();
        $activeRowMock->expects('offsetGet')->with('activated')->andReturn(1);
        $activeRowMock->expects('offsetGet')->with('inactive')->andReturn(0);
        $activeRowMock->expects('offsetGet')->with('json')->andReturn('{"id":"1"}');
        $activeRowMock->expects('offsetGet')->with('intPrimaryKey')->andReturn(10);

        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);

        $beanInstance = new class($activeRowMock) extends \CoolBeans\Bean {
            public int $id;
            public bool $active = true;
            public ?bool $ready;
            public bool $activated;
            public bool $inactive;
            public \Infinityloop\Utils\Json $json;
            public \CoolBeans\PrimaryKey\IntPrimaryKey $intPrimaryKey;
        };

        self::assertEquals($this->activeRowData['id'], $beanInstance->offsetGet('id'));
        self::assertEquals(true, $beanInstance->offsetGet('active'));
        self::assertEquals(false, $beanInstance->offsetGet('ready'));
        self::assertEquals(true, $beanInstance->offsetGet('activated'));
        self::assertEquals(false, $beanInstance->offsetGet('inactive'));
        self::assertInstanceOf(\Infinityloop\Utils\Json::class, $beanInstance->offsetGet('json'));
        self::assertEquals('{"id":"1"}', $beanInstance->offsetGet('json')->toString());
        self::assertEquals(true, (new \ReflectionMethod(\CoolBeans\Bean::class, 'initiateProperties'))->isProtected());
        self::assertEquals(10, $beanInstance->offsetGet('intPrimaryKey')->getValue());
    }

    public function testInitiatePropertiesPropertyWithoutType() : void
    {
        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('offsetGet')->with('id')->andReturn($this->activeRowData['id']);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);

        $this->expectException(\CoolBeans\Exception\MissingType::class);
        $this->expectExceptionMessage('Property [id] does not have type.');

        new class($activeRowMock) extends \CoolBeans\Bean {
            public $id;
        };
    }

    public function testInitiatePropertiesPropertyWithoutNullable() : void
    {
        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('offsetGet')->with('nulled')->andReturn(null);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);

        $this->expectException(\CoolBeans\Exception\NonNullableType::class);
        $this->expectExceptionMessage('Property [nulled] does not have nullable type.');

        new class($activeRowMock) extends \CoolBeans\Bean {
            public bool $nulled;
        };
    }

    public function testValidateMissingColumns() : void
    {
        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('offsetGet')->with('id')->andReturn($this->activeRowData['id']);
        $activeRowMock->expects('toArray')->withNoArgs()->andReturn($this->activeRowData);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);

        $beanInstance = new class($activeRowMock) extends \CoolBeans\Bean {
            public int $id;

            public function callValidateMissingColumns()
            {
                $this->validateMissingColumns();
            }
        };

        $beanInstance->callValidateMissingColumns();
    }

    public function testValidateMissingColumnsWithoutPropertyDefinition() : void
    {
        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('toArray')->withNoArgs()->andReturn($this->activeRowData);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);

        $beanInstance = new class($activeRowMock) extends \CoolBeans\Bean {
            public function callValidateMissingColumns()
            {
                $this->validateMissingColumns();
            }
        };

        $this->expectException(\CoolBeans\Exception\MissingProperty::class);
        $this->expectExceptionMessage('Property for column [id] is not defined.');

        $beanInstance->callValidateMissingColumns();
    }

    public function testValidateTableName() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('getName')->withNoArgs()->andReturn('test_bean');

        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getTable')->withNoArgs()->andReturn($selectionMock);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);

        $testBeanInstance = new TestBean($activeRowMock);

        $testBeanInstance->callValidateTableName();
    }

    public function testValidateTableNameRelatedSyntax() : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('getName')->withNoArgs()->andReturn('user.test_bean');

        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getTable')->withNoArgs()->andReturn($selectionMock);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);

        $testBeanInstance = new TestBean($activeRowMock);

        $testBeanInstance->callValidateTableName();
    }

    public function validateTableNameIncorrectNameDataProvider() : array
    {
        return [
            ['TEST_BEAN'],
            ['test_Bean'],
            ['Test_bean'],
            ['Test_Bean'],
        ];
    }

    /**
     * @dataProvider validateTableNameIncorrectNameDataProvider
     */
    public function testValidateTableNameIncorrectName(string $tableName) : void
    {
        $selectionMock = \Mockery::mock(\Nette\Database\Table\Selection::class);
        $selectionMock->expects('getName')->withNoArgs()->andReturn($tableName);

        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getTable')->withNoArgs()->andReturn($selectionMock);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => $this->activeRowData['id']]);

        $testBeanInstance = new TestBean($activeRowMock);

        $this->expectException(\CoolBeans\Exception\InvalidTable::class);

        $testBeanInstance->callValidateTableName();
    }
}