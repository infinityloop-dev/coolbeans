<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\TestBean;

//@phpcs:disable SlevomatCodingStandard.Classes.ClassStructure.IncorrectGroupOrder
//@phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedProperty
final class SimpleBeanAttribute extends \CoolBeans\Bean
{
    private int $col1;
    protected string $col2 = 'default';
    public \CoolBeans\PrimaryKey\IntPrimaryKey $id;
    #[\CoolBeans\Attribute\UniqueConstraint]
    public string $col3;
    public ?string $col4;
    public ?string $col5 = null;
    public ?string $col6 = 'default';
    public \DateTime $col8;
    public \Nette\Utils\DateTime $col9;
}
