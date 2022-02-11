<?php

declare(strict_types = 1);

namespace CoolBeans\Tests\Unit\TestBean;

enum SimpleEnum : string
{
    case ABC = 'abc';
    case BCA = 'bca';
    case XYZ = 'xyz';
}
