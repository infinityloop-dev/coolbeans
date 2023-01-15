<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\TestBean;

#[\CoolBeans\Attribute\PrimaryKey('col1', 'col2')]
final class TwoPrimaryKeysBean extends \CoolBeans\Bean
{
    public int $col1;
    public int $col2;
}
