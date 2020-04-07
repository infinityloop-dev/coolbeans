<?php

declare(strict_types=1);

namespace CoolBeans\Tests\Unit\Decorator;

final class BeanTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testGetRow() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(10);

        $activeRowMock = \Mockery::mock(\CoolBeans\Bridge\Nette\ActiveRow::class);
        $activeRowMock->expects('getPrimary')->with(false)->andReturn(['id' => 10]);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('getRow')->with($primaryKey)->andReturn($activeRowMock);

        $beanInstance = new \CoolBeans\Decorator\Bean(
            $dataSourceMock,
            \CoolBeans\Tests\Unit\TestBean::class,
            \CoolBeans\Tests\Unit\TestSelection::class
        );

        $beanInstance->getRow($primaryKey);
    }

    public function testFindAll() : void
    {
        $selectionMock = \Mockery::mock(\CoolBeans\Bridge\Nette\Selection::class);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('findAll')->withNoArgs()->andReturn($selectionMock);

        $beanInstance = new \CoolBeans\Decorator\Bean(
            $dataSourceMock,
            \CoolBeans\Tests\Unit\TestBean::class,
            \CoolBeans\Tests\Unit\TestSelection::class
        );

        $beanInstance->findAll();
    }

    public function testFindByArray() : void
    {
        $filter = ['active' => 1];

        $selectionMock = \Mockery::mock(\CoolBeans\Bridge\Nette\Selection::class);

        $dataSourceMock = \Mockery::mock(\CoolBeans\Contract\DataSource::class);
        $dataSourceMock->expects('findByArray')->with($filter)->andReturn($selectionMock);

        $beanInstance = new \CoolBeans\Decorator\Bean(
            $dataSourceMock,
            \CoolBeans\Tests\Unit\TestBean::class,
            \CoolBeans\Tests\Unit\TestSelection::class
        );

        $beanInstance->findByArray($filter);
    }

    public function testInvalidRowClass() : void
    {
        $this->expectException(\CoolBeans\Exception\InvalidFunctionParameters::class);

        new \CoolBeans\Decorator\Bean(
            \Mockery::mock(\CoolBeans\Contract\DataSource::class),
            \CoolBeans\Bean::class,
            \CoolBeans\Tests\Unit\TestSelection::class
        );
    }

    public function testInvalidSelectionClass() : void
    {
        $this->expectException(\CoolBeans\Exception\InvalidFunctionParameters::class);

        new \CoolBeans\Decorator\Bean(
            \Mockery::mock(\CoolBeans\Contract\DataSource::class),
            \CoolBeans\Tests\Unit\TestBean::class,
            \CoolBeans\Bean::class
        );
    }
}