<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit;

//@phpcs:disable SlevomatCodingStandard.Classes.ClassStructure.IncorrectGroupOrder
//@phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedProperty
class AttributeBean extends \CoolBeans\Bean
{
    #[\CoolBeans\Attribute\DefaultValue(\CoolBeans\Attribute\Defaults\MysqlDefaults::NOW)]
    #[\CoolBeans\Attribute\TypeOverride(\CoolBeans\Attribute\Types\MysqlTypes::DATE)]
    private int $col1;
    #[\CoolBeans\Attribute\DefaultValue(\CoolBeans\Attribute\Defaults\MysqlDefaults::NOW)]
    #[\CoolBeans\Attribute\TypeOverride(\CoolBeans\Attribute\Types\MysqlTypes::DATE)]
    public \DateTime $col2;
    #[\CoolBeans\Attribute\DefaultValue(\CoolBeans\Attribute\Defaults\MysqlDefaults::NOW)]
    #[\CoolBeans\Attribute\TypeOverride(\CoolBeans\Attribute\Types\MysqlTypes::TIME)]
    public \Nette\Utils\DateTime $col3;
    #[\CoolBeans\Attribute\TypeOverride(\CoolBeans\Attribute\Types\MysqlTypes::BIGINT)]
    public int $col4;
    #[\CoolBeans\Attribute\TypeOverride(\CoolBeans\Attribute\Types\MysqlTypes::INT)]
    public float $col5 = 1;
}
