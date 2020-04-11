<?php

declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

namespace App;

class Bootstrap
{
    public static function isDebugMode() : bool
    {
        return false;
    }
}
