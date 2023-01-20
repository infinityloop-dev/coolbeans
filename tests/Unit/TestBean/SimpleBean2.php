<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\TestBean;

//@phpcs:disable SlevomatCodingStandard.Classes.ClassStructure.IncorrectGroupOrder
//@phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedProperty
#[\CoolBeans\Attribute\ClassCheckConstraint('IF(`col3` = \'abc\', `col4` IS NOT NULL, TRUE)')]
final class SimpleBean2 extends \CoolBeans\Bean
{
    private int $col1;
    protected string $col2 = 'default';
    public \CoolBeans\PrimaryKey\IntPrimaryKey $id;
    #[\CoolBeans\Attribute\CheckConstraint('CHAR_LENGTH(`col3`) > 3')]
    public string $col3;
    public ?string $col4;
    #[\CoolBeans\Attribute\AllowEmptyString]
    public ?string $col5 = null;
    public ?string $col6 = 'default';
    public \DateTime $col8;
    public \Nette\Utils\DateTime $col9;
    public const ABC = 0;
}
