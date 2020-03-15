<?php

declare(strict_types = 1);

namespace Infinityloop\CoolBeans\Utils;

class Routing
{
    use \Nette\StaticClass;

    public static function filterIn(array $parameters) : array
    {
        foreach ($parameters as $key => $param) {
            if ($param !== null && self::isPrimaryKey($key)) {
                if (!\is_int($param) || !\is_numeric($param)) {
                    throw new \Infinityloop\CoolBeans\Exception\InvalidFunctionParameters('Id parameters needs to be integers.');
                }

                $parameters[$key] = new \Infinityloop\CoolBeans\PrimaryKey\IntPrimaryKey((int) $param);
            }
        }

        return $parameters;
    }

    public static function filterOut(array $parameters) : array
    {
        foreach ($parameters as $key => $param) {
            if ($param !== null && self::isPrimaryKey($key)) {
                if (!$param instanceof \Infinityloop\CoolBeans\Contract\PrimaryKey) {
                    throw new \Infinityloop\CoolBeans\Exception\InvalidFunctionParameters('Ids are expected to be instanceof PrimaryKey');
                }

                $parameters[$key] = $param->printValue();
            }
        }

        return $parameters;
    }

    public static function isPrimaryKey(string $paramName) : bool
    {
        return $paramName === 'id'
            || \Nette\Utils\Strings::endsWith($paramName, 'Id')
            || \Nette\Utils\Strings::endsWith($paramName, '-id');
    }
}
