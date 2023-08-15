<?php

declare(strict_types=1);

namespace MoonShine\Fields;

/**
 * @method static static make(?string $label = null, ?string $column = null)
 */
class Position extends NoInput
{
    public function __construct(?string $label = null, ?string $column = null)
    {
        parent::__construct($label ?? '#', $column, static fn ($item, $index) => $index + 1);
    }
}
