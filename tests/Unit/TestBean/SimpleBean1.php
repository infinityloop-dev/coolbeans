<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\TestBean;

//@phpcs:disable SlevomatCodingStandard.Classes.ClassStructure.IncorrectGroupOrder
//@phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedProperty
//@phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
final class SimpleBean1 extends \CoolBeans\Bean
{
    private int $col1;
    protected string $col2 = 'default';
    public \CoolBeans\PrimaryKey\IntPrimaryKey $id;
    public string $col3;
    public ?string $col4;
    public ?string $col5 = null;
    public ?string $col6 = 'default';
    public \CoolBeans\PrimaryKey\IntPrimaryKey $simple_bean_2_id;
    public \DateTime $col8;
    public \Nette\Utils\DateTime $col9;
    public int $col10 = 5;
    public float $col11 = 0.005;
}
