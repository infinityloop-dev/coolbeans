<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\TestBean;

//@phpcs:disable SlevomatCodingStandard.Classes.ClassStructure.IncorrectGroupOrder
//@phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedProperty
//@phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
#[\CoolBeans\Attribute\ClassUniqueConstraint(['col2', 'col3'])]
#[\CoolBeans\Attribute\ClassUniqueConstraint(['col4', 'col5', 'col6'])]
#[\CoolBeans\Attribute\Comment('Some random comment')]
final class AttributeBean extends \CoolBeans\Bean
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
    #[\CoolBeans\Attribute\UniqueConstraint]
    public int $col4;
    #[\CoolBeans\Attribute\TypeOverride(\CoolBeans\Attribute\Types\MysqlTypes::INT, 44)]
    #[\CoolBeans\Attribute\UniqueConstraint]
    #[\CoolBeans\Attribute\Comment('Some random comment')]
    public float $col5 = 1;
    #[\CoolBeans\Attribute\TypeOverride(\CoolBeans\Attribute\Types\MysqlTypes::DECIMAL, 1, 3)]
    #[\CoolBeans\Attribute\UniqueConstraint]
    public float $col6 = 1.005;
    #[\CoolBeans\Attribute\ForeignKeyConstraint(
        null,
        null,
    )]
    public \CoolBeans\PrimaryKey\IntPrimaryKey $col7_id;
    #[\CoolBeans\Attribute\TypeOverride(\CoolBeans\Attribute\Types\MysqlTypes::DOUBLE, 16, 2)]
    public float $col8;
    #[\CoolBeans\Attribute\ForeignKey('test_table', 'test_column')]
    #[\CoolBeans\Attribute\ForeignKeyConstraint(
        null,
        \CoolBeans\Attribute\Types\MysqlForeignKeyConstraintTypes::RESTRICT,
    )]
    public \CoolBeans\PrimaryKey\IntPrimaryKey $col9_id;
    #[\CoolBeans\Attribute\ForeignKey('test_table')]
    #[\CoolBeans\Attribute\ForeignKeyConstraint(
        \CoolBeans\Attribute\Types\MysqlForeignKeyConstraintTypes::RESTRICT,
        \CoolBeans\Attribute\Types\MysqlForeignKeyConstraintTypes::RESTRICT,
    )]
    public \CoolBeans\PrimaryKey\IntPrimaryKey $col10_id;
}
