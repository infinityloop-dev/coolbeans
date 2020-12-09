<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class TypeOverride
{
    public array $lengthArgs;

    public function __construct(
        public string $type,
        int ...$lengthArgs
    )
    {
        $this->lengthArgs = $lengthArgs;
    }

    public function getType() : string
    {
        if (\count($this->lengthArgs) === 0) {
            return $this->type;
        }

        return $this->type . '(' . \implode(',', $this->lengthArgs) . ')';
    }
}
