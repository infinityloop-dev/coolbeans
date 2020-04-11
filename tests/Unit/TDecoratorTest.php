<?php

declare(strict_types=1);

namespace CoolBeans\Tests\Unit;

final class TDecoratorTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testGetRow() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(10);

        $activeRowMock = \Mockery::mock(\Nette\Database\Table\ActiveRow::class);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => 10]);

        $testBeanInstance = new TestBean($activeRowMock);

        $dataSourceMock = \Mockery::mock(\CoolBeans\DataSource::class);
        $dataSourceMock->expects('getRow')->with($primaryKey)->andReturn($testBeanInstance);

        $decoratorInstance = new class($dataSourceMock) {
            use \CoolBeans\TDecorator;

            public function __construct(\CoolBeans\Contract\DataSource $dataSource)
            {
                $this->dataSource = $dataSource;
            }
        };

        self::assertEquals($testBeanInstance, $decoratorInstance->getRow($primaryKey));
    }

    public function testFindAll() : void
    {
        $selectionMock = \Mockery::mock(\CoolBeans\Selection::class);

        $dataSourceMock = \Mockery::mock(\CoolBeans\DataSource::class);
        $dataSourceMock->expects('findAll')->withNoArgs()->andReturn($selectionMock);
        $decoratorInstance = new class($dataSourceMock) {
            use \CoolBeans\TDecorator;

            public function __construct(\CoolBeans\Contract\DataSource $dataSource)
            {
                $this->dataSource = $dataSource;
            }
        };

        self::assertEquals($selectionMock, $decoratorInstance->findAll());
    }

    public function testFindByArray() : void
    {
        $selectionMock = \Mockery::mock(\CoolBeans\Selection::class);

        $dataSourceMock = \Mockery::mock(\CoolBeans\DataSource::class);
        $dataSourceMock->expects('findByArray')->with(['active' => 0])->andReturn($selectionMock);
        $decoratorInstance = new class($dataSourceMock) {
            use \CoolBeans\TDecorator;

            public function __construct(\CoolBeans\Contract\DataSource $dataSource)
            {
                $this->dataSource = $dataSource;
            }
        };

        self::assertEquals($selectionMock, $decoratorInstance->findByArray(['active' => 0]));
    }
}