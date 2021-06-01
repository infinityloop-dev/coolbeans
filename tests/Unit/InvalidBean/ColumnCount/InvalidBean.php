<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\InvalidBean\ColumnCount;

//@phpcs:disable SlevomatCodingStandard.Classes.ClassStructure.IncorrectGroupOrder
//@phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedProperty
#[\CoolBeans\Attribute\ClassUniqueConstraint(['col4'])]
final class InvalidBean extends \CoolBeans\Bean
{
    private int $col1;
    protected string $col2 = 'default';
    public \CoolBeans\PrimaryKey\IntPrimaryKey $id;
    public string $col3;
    public ?string $col4;
    public ?string $col5 = null;
    public ?string $col6 = 'default';
    public \DateTime $col8;
    public \Nette\Utils\DateTime $col9;
}
