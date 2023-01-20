<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\TestBean;

//@phpcs:disable SlevomatCodingStandard.Classes.ClassStructure.IncorrectGroupOrder
//@phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedProperty
#[\CoolBeans\Attribute\ClassUniqueConstraint(['col4', 'col5'])]
#[\CoolBeans\Attribute\Collation('cz_collation')]
#[\CoolBeans\Attribute\Charset('cz_charset')]
final class SimpleBeanClassAttribute extends \CoolBeans\Bean
{
    private int $col1;
    protected string $col2 = 'default';
    public \CoolBeans\PrimaryKey\IntPrimaryKey $id;
    #[\CoolBeans\Attribute\AllowEmptyString]
    public string $col3;
    #[\CoolBeans\Attribute\AllowEmptyString]
    public ?string $col4;
    #[\CoolBeans\Attribute\AllowEmptyString]
    public ?string $col5 = null;
    #[\CoolBeans\Attribute\AllowEmptyString]
    public ?string $col6 = 'default';
    public \DateTime $col8;
    public \Nette\Utils\DateTime $col9;
}
