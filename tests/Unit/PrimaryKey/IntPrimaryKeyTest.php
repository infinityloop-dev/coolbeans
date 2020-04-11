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
}