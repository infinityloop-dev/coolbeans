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
}