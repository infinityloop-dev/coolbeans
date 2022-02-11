<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute\Types;

enum Order : string
{
    case ASC = 'ASC';
    case DESC = 'DESC';
}
