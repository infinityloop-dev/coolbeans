<?php

declare(strict_types = 1);

namespace CoolBeans\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS)]
final class Comment
{
    public function __construct(
        public string $comment,
    )
    {
    }
}
