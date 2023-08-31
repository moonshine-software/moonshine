<?php

namespace MoonShine\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Color
{
    public function __construct(public string $color)
    {
    }
}
