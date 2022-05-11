<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\InvalidBean\PrimaryKeyType;

//@phpcs:disable SlevomatCodingStandard.Classes.ClassStructure.IncorrectGroupOrder
//@phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedProperty
final class InvalidBean extends \CoolBeans\Bean
{
    public int $id;
    #[\CoolBeans\Attribute\PrimaryKey]
    public int $code;
}
