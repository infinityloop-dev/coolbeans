<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class TypeOverride
{
    public array $lengthArgs;

    public function __construct(
        public \CoolBeans\Attribute\Types\ColumnType $type,
        int ...$lengthArgs,
    )
    {
        $this->lengthArgs = $lengthArgs;
    }

    public function getType() : string
    {
        if (\count($this->lengthArgs) === 0) {
            return $this->type->value;
        }

        return $this->type->value . '(' . \implode(', ', $this->lengthArgs) . ')';
    }
}
