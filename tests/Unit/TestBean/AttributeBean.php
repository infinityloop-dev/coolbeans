<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\TestBean;

use CoolBeans\Attribute\Types\ColumnType;
use CoolBeans\Attribute\Types\DefaultFunction;
use CoolBeans\Attribute\Types\ForeignKeyConstraintType;
use CoolBeans\Attribute\Types\Order;

//@phpcs:disable SlevomatCodingStandard.Classes.ClassStructure.IncorrectGroupOrder
//@phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedProperty
//@phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
#[\CoolBeans\Attribute\ClassUniqueConstraint(['col2', 'col3'])]
#[\CoolBeans\Attribute\ClassUniqueConstraint(['col4', 'col5', 'col6'])]
#[\CoolBeans\Attribute\ClassIndex(['col4', 'col5', 'col6'], [null, Order::DESC, Order::ASC])]
#[\CoolBeans\Attribute\Comment('Some random comment')]
#[\CoolBeans\Attribute\PrimaryKey('code')]
final class AttributeBean extends \CoolBeans\Bean
{
    #[\CoolBeans\Attribute\DefaultValue(DefaultFunction::NOW)]
    #[\CoolBeans\Attribute\TypeOverride(ColumnType::DATE)]
    private int $col1;
    #[\CoolBeans\Attribute\DefaultValue(DefaultFunction::NOW)]
    #[\CoolBeans\Attribute\TypeOverride(ColumnType::DATE)]
    public \DateTime $col2;
    #[\CoolBeans\Attribute\DefaultValue(DefaultFunction::NOW)]
    #[\CoolBeans\Attribute\TypeOverride(ColumnType::TIME)]
    public \Nette\Utils\DateTime $col3;
    #[\CoolBeans\Attribute\TypeOverride(ColumnType::BIGINT)]
    #[\CoolBeans\Attribute\UniqueConstraint]
    public int $col4;
    #[\CoolBeans\Attribute\TypeOverride(ColumnType::INT, 44)]
    #[\CoolBeans\Attribute\UniqueConstraint]
    #[\CoolBeans\Attribute\Comment('Some random comment')]
    #[\CoolBeans\Attribute\Index]
    public float $col5 = 1;
    #[\CoolBeans\Attribute\TypeOverride(ColumnType::DECIMAL, 1, 3)]
    #[\CoolBeans\Attribute\UniqueConstraint]
    #[\CoolBeans\Attribute\Index(Order::ASC)]
    public float $col6 = 1.005;
    #[\CoolBeans\Attribute\ForeignKeyConstraint(null, null)]
    public \CoolBeans\PrimaryKey\IntPrimaryKey $simple_bean_2_id;
    #[\CoolBeans\Attribute\TypeOverride(ColumnType::DOUBLE, 16, 2)]
    #[\CoolBeans\Attribute\Index(Order::DESC)]
    public float $col8;
    #[\CoolBeans\Attribute\ForeignKey('simple_bean_2', 'id')]
    #[\CoolBeans\Attribute\ForeignKeyConstraint(null, ForeignKeyConstraintType::RESTRICT)]
    public \CoolBeans\PrimaryKey\IntPrimaryKey $col9_id;
    #[\CoolBeans\Attribute\ForeignKey('simple_bean_2')]
    #[\CoolBeans\Attribute\ForeignKeyConstraint(ForeignKeyConstraintType::RESTRICT, ForeignKeyConstraintType::RESTRICT)]
    public \CoolBeans\PrimaryKey\IntPrimaryKey $col10_id;
    public string $code;
    public \CoolBeans\PrimaryKey\IntPrimaryKey $id;
}
