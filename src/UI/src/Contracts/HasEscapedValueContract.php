<?php

declare(strict_types=1);

namespace MoonShine\UI\Contracts;

/**
 * Fields that escape their own value, supplied by the WithEscapedValue trait.
 */
interface HasEscapedValueContract
{
    public function escape(): static;

    public function unescape(): static;

    public function isUnescape(): bool;
}
