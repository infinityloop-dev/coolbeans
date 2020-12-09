<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\Decorator;

final class TDecoratorTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testGetRow() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(10);

        $activeRowMock = \Mockery::mock(\CoolBeans\Bridge\Nette\ActiveRow::class);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('getRow')->with($primaryKey)->andReturn($activeRowMock);

        $decoratorInstance = new class ($dataSourceMock) {
            use \CoolBeans\Decorator\TDecorator;

            public function __construct(\CoolBeans\Contract\DataSource $dataSource)
            {
                $this->dataSource = $dataSource;
            }
        };

        self::assertEquals($activeRowMock, $decoratorInstance->getRow($primaryKey));
    }

    public function testFindAll() : void
    {
        $selectionMock = \Mockery::mock(\CoolBeans\Bridge\Nette\Selection::class);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('findAll')->withNoArgs()->andReturn($selectionMock);
        $decoratorInstance = new class ($dataSourceMock) {
            use \CoolBeans\Decorator\TDecorator;

            public function __construct(\CoolBeans\Contract\DataSource $dataSource)
            {
                $this->dataSource = $dataSource;
            }
        };

        self::assertEquals($selectionMock, $decoratorInstance->findAll());
    }

    public function testFindByArray() : void
    {
        $selectionMock = \Mockery::mock(\CoolBeans\Bridge\Nette\Selection::class);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('findByArray')->with(['active' => 0])->andReturn($selectionMock);
        $decoratorInstance = new class ($dataSourceMock) {
            use \CoolBeans\Decorator\TDecorator;

            public function __construct(\CoolBeans\Contract\DataSource $dataSource)
            {
                $this->dataSource = $dataSource;
            }
        };

        self::assertEquals($selectionMock, $decoratorInstance->findByArray(['active' => 0]));
    }
}
