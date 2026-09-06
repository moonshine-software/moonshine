<?php

declare(strict_types=1);

namespace MoonShine\Support\Traits;

trait Makeable
{
    public static function make(mixed ...$arguments): static
    {
        // @phpstan-ignore new.static, argument.type, return.type (Constructor arguments are described by each class's make() PHPDoc.)
        return new static(...$arguments);
    }
}
