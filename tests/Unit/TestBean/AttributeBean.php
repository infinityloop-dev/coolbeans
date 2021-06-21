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
    #[\CoolBeans\Attribute\TypeOverride(\CoolBeans\Attribute\Types\MysqlType::DATE)]
    private int $col1;
    #[\CoolBeans\Attribute\DefaultValue(\CoolBeans\Attribute\Defaults\MysqlDefaults::NOW)]
    #[\CoolBeans\Attribute\TypeOverride(\CoolBeans\Attribute\Types\MysqlType::DATE)]
    public \DateTime $col2;
    #[\CoolBeans\Attribute\DefaultValue(\CoolBeans\Attribute\Defaults\MysqlDefaults::NOW)]
    #[\CoolBeans\Attribute\TypeOverride(\CoolBeans\Attribute\Types\MysqlType::TIME)]
    public \Nette\Utils\DateTime $col3;
    #[\CoolBeans\Attribute\TypeOverride(\CoolBeans\Attribute\Types\MysqlType::BIGINT)]
    #[\CoolBeans\Attribute\UniqueConstraint]
    public int $col4;
    #[\CoolBeans\Attribute\TypeOverride(\CoolBeans\Attribute\Types\MysqlType::INT, 44)]
    #[\CoolBeans\Attribute\UniqueConstraint]
    #[\CoolBeans\Attribute\Comment('Some random comment')]
    public float $col5 = 1;
    #[\CoolBeans\Attribute\TypeOverride(\CoolBeans\Attribute\Types\MysqlType::DECIMAL, 1, 3)]
    #[\CoolBeans\Attribute\UniqueConstraint]
    public float $col6 = 1.005;
    #[\CoolBeans\Attribute\ForeignKeyConstraint(
        null,
        null,
    )]
    public \CoolBeans\PrimaryKey\IntPrimaryKey $col7_id;
    #[\CoolBeans\Attribute\TypeOverride(\CoolBeans\Attribute\Types\MysqlType::DOUBLE, 16, 2)]
    public float $col8;
    #[\CoolBeans\Attribute\ForeignKey('test_table', 'test_column')]
    #[\CoolBeans\Attribute\ForeignKeyConstraint(
        null,
        \CoolBeans\Attribute\Types\MysqlForeignKeyConstraintType::RESTRICT,
    )]
    public \CoolBeans\PrimaryKey\IntPrimaryKey $col9_id;
    #[\CoolBeans\Attribute\ForeignKey('test_table')]
    #[\CoolBeans\Attribute\ForeignKeyConstraint(
        \CoolBeans\Attribute\Types\MysqlForeignKeyConstraintType::RESTRICT,
        \CoolBeans\Attribute\Types\MysqlForeignKeyConstraintType::RESTRICT,
    )]
    public \CoolBeans\PrimaryKey\IntPrimaryKey $col10_id;
}
