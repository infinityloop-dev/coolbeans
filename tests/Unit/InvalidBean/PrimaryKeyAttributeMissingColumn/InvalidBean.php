<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\InvalidBean\PrimaryKeyAttributeMissingColumn;

//@phpcs:disable SlevomatCodingStandard.Classes.ClassStructure.IncorrectGroupOrder
//@phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedProperty
#[\CoolBeans\Attribute\PrimaryKey('unknown')]
final class InvalidBean extends \CoolBeans\Bean
{
    public \CoolBeans\PrimaryKey\IntPrimaryKey $id;
    public \CoolBeans\PrimaryKey\IntPrimaryKey $code;
}
