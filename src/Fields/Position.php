<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;

/**
 * @method static static make(Closure|string|null $label = null, ?string $column = null)
 */
class Position extends NoInput
{
    public function __construct(?string $label = null, ?string $column = null)
    {
        parent::__construct($label ?? '#', $column, static fn ($item, $index): int|float => $index + 1);

        $this->customAttributes([
            'data-increment-position' => true
        ]);
    }
}
