<?php

namespace MoonShine\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Icon
{
    public function __construct(public string $icon)
    {
    }
}
