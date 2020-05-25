<?php

declare(strict_types=1);

namespace CoolBeans\Tests\Unit\PrimaryKey;

final class IntPrimaryKeyTest extends \PHPUnit\Framework\TestCase
{
    public function testGetValue() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(10);

        self::assertEquals(10, $primaryKey->getValue());
    }

    public function testPrintValue() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(15);

        self::assertEquals('15', $primaryKey->printValue());
    }

    public function testGetNameDefault() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(10);

        self::assertEquals('id', $primaryKey->getName());
    }

    public function testGetNameCustom() : void
    {
        $primaryKey = new \CoolBeans\PrimaryKey\IntPrimaryKey(10, 'name_column');

        self::assertEquals('name_column', $primaryKey->getName());
    }

    public function testInvalidKeyValue() : void
    {
        $this->expectException(\CoolBeans\Exception\InvalidFunctionParameters::class);

        new \CoolBeans\PrimaryKey\IntPrimaryKey(0);
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
                new \CoolBeans\PrimaryKey\IntPrimaryKey(10),
                new \CoolBeans\PrimaryKey\IntPrimaryKey(10),
                true,
            ],
            [
                new \CoolBeans\PrimaryKey\IntPrimaryKey(10, 'active'),
                new \CoolBeans\PrimaryKey\IntPrimaryKey(10, 'active'),
                true,
            ],
            [
                new \CoolBeans\PrimaryKey\IntPrimaryKey(10),
                new \CoolBeans\PrimaryKey\IntPrimaryKey(10, 'id'),
                true,
            ],
            [
                new \CoolBeans\PrimaryKey\IntPrimaryKey(10),
                new \CoolBeans\PrimaryKey\IntPrimaryKey(11),
                false,
            ],
            [
                new \CoolBeans\PrimaryKey\IntPrimaryKey(10),
                new \CoolBeans\PrimaryKey\IntPrimaryKey(10, 'active'),
                false,
            ],
        ];
    }
}