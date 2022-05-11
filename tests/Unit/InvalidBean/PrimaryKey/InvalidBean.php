<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\InvalidBean\PrimaryKey;

//@phpcs:disable SlevomatCodingStandard.Classes.ClassStructure.IncorrectGroupOrder
//@phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedProperty
final class InvalidBean extends \CoolBeans\Bean
{
    public \CoolBeans\PrimaryKey\IntPrimaryKey $id;
    #[\CoolBeans\Attribute\PrimaryKey]
    public \CoolBeans\PrimaryKey\IntPrimaryKey $code;
}
