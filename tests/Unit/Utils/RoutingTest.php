<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\Utils;

final class RoutingTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testFilterIn() : void
    {
        $inputParameters = [
            'id' => 1,
            'data-id' => 2,
            'dataId' => '3',
            'shrek' => 4,
        ];

        $outputParameters = [
            'id' => new \CoolBeans\PrimaryKey\IntPrimaryKey(1),
            'data-id' => new \CoolBeans\PrimaryKey\IntPrimaryKey(2),
            'dataId' => new \CoolBeans\PrimaryKey\IntPrimaryKey(3),
            'shrek' => 4,
        ];

        self::assertEquals($outputParameters, \CoolBeans\Utils\Routing::filterIn($inputParameters));
    }

    public function testFilterInInvalidParameter() : void
    {
        $this->expectException(\CoolBeans\Exception\InvalidFunctionParameters::class);
        $this->expectExceptionMessage('Id parameters needs to be integers.');

        \CoolBeans\Utils\Routing::filterIn(['id' => 'shrek']);
    }

    public function testFilterOut() : void
    {
        $inputParameters = [
            'id' => new \CoolBeans\PrimaryKey\IntPrimaryKey(1),
            'data-id' => new \CoolBeans\PrimaryKey\IntPrimaryKey(2),
            'dataId' => new \CoolBeans\PrimaryKey\IntPrimaryKey(3),
            'shrek' => new \CoolBeans\PrimaryKey\IntPrimaryKey(4),
        ];

        $outputParameters = [
            'id' => 1,
            'data-id' => 2,
            'dataId' => 3,
            'shrek' => new \CoolBeans\PrimaryKey\IntPrimaryKey(4),
        ];

        self::assertEquals($outputParameters, \CoolBeans\Utils\Routing::filterOut($inputParameters));
    }

    public function testFilterOutInvalidParameter() : void
    {
        $this->expectException(\CoolBeans\Exception\InvalidFunctionParameters::class);
        $this->expectExceptionMessage('Ids are expected to be instanceof PrimaryKey');

        \CoolBeans\Utils\Routing::filterOut(['id' => 'shrek']);
    }

    public function isPrimaryKeyDataProvider() : array
    {
        return [
            ['id', true],
            ['rowId', true],
            ['row-id', true],
            ['changedIds', false],
            ['allowed-ids', false],
            ['ids', false],
        ];
    }

    /**
     * @dataProvider isPrimaryKeyDataProvider
     */
    public function testIsPrimaryKey(string $input, bool $output) : void
    {
        self::assertEquals($output, \CoolBeans\Utils\Routing::isPrimaryKey($input));
    }
}
