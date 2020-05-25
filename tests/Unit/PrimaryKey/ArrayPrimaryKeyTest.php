<?php

declare(strict_types=1);

namespace CoolBeans\Tests\Unit\PrimaryKey;

final class ArrayPrimaryKeyTest extends \PHPUnit\Framework\TestCase
{
    public function testGetValue() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\ArrayPrimaryKey(['id' => 10]);

        self::assertEquals(['id' => 10], $primaryKey->getValue());
    }

    public function testPrintValue() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\ArrayPrimaryKey(['id' => 15]);

        self::assertEquals(15, $primaryKey->printValue());
    }

    public function testGetNameDefault() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\ArrayPrimaryKey(['id' => 10]);

        self::assertEquals('id', $primaryKey->getName());
    }

    public function testGetNameCustom() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\ArrayPrimaryKey(['name_column' => 10]);

        self::assertEquals('name_column', $primaryKey->getName());
    }

    /**
     * @dataProvider equalsDataProvider
     * @param \CoolBeans\Contract\PrimaryKey $key
     * @param \CoolBeans\Contract\PrimaryKey $compare
     * @param bool $expectedOutput
     */
    public function testEquals(\CoolBeans\Contract\PrimaryKey $key, \CoolBeans\Contract\PrimaryKey $compare, bool $expectedOutput) : void
    {
        self::assertEquals($expectedOutput, $key->equals($compare));
    }

    public function equalsDataProvider() : array
    {
        return [
            [
                new \CoolBeans\PrimaryKey\ArrayPrimaryKey(['id' => 12, 'active' => 1]),
                new \CoolBeans\PrimaryKey\ArrayPrimaryKey(['id' => 12, 'active' => 1]),
                true,
            ],
            [
                new \CoolBeans\PrimaryKey\ArrayPrimaryKey(['id' => 12]),
                new \CoolBeans\PrimaryKey\ArrayPrimaryKey(['id' => 12, 'active' => 1]),
                false,
            ],
            [
                new \CoolBeans\PrimaryKey\ArrayPrimaryKey(['id' => 12, 'active' => 1]),
                new \CoolBeans\PrimaryKey\ArrayPrimaryKey(['active' => 1, 'id' => 12]),
                false,
            ],
        ];
    }
}