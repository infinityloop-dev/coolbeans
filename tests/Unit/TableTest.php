<?php

declare(strict_types=1);

namespace CoolBeans\Tests\Unit;

final class TableTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testConstructInvalidParameters() : void
    {
        $this->expectException(\CoolBeans\Exception\InvalidFunctionParameters::class);
        $this->expectExceptionMessage('Either context or its factory must be provided.');

        new class('table_name', null, null) extends \CoolBeans\Table {};
    }

    public function testGetName() : void
    {
        $contextFactory = new class implements \CoolBeans\Contract\ContextFactory{
            public function create(): \Nette\Database\Context
            {
                return \Mockery::mock(\Nette\Database\Context::class);
            }
        };
        $tableInstance = new class('table_name', null, $contextFactory) extends \CoolBeans\Table {};

        self::assertEquals('table_name', $tableInstance->getName());
    }

    public function testGetRow() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(10);

        $conventionInstance = new class implements \Nette\Database\IConventions {
            function getPrimary(string $table) {}
            function getHasManyReference(string $table, string $key): ?array { return null; }
            function getBelongsToReference(string $table, string $key): ?array { return null; }
        };

        $contextMock = \Mockery::mock(\Nette\Database\Context::class);
        $contextMock->expects('getConventions')->withNoArgs()->andReturn($conventionInstance);

        $contextFactory  = \Mockery::mock(\CoolBeans\Contract\ContextFactory::class);
        $contextFactory->expects('create')->withNoArgs()->andReturn($contextMock);

        $tableInstance = new class('table_name', null, $contextFactory) extends \CoolBeans\Table {};

        $tableInstance->getRow($primaryKey);
    }
}