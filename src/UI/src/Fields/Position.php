<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\Support\Stringify;

/**
 * @method static static make(Closure|string|null $label = null, ?string $column = null)
 */
class Position extends Preview
{
    public function __construct(?string $label = null, ?string $column = null)
    {
        parent::__construct($label ?? '#', $column, static fn ($item, $index): int => $index + 1);

        $this->customAttributes([
            'data-increment-position' => true,
        ]);
    }

    protected function resolveValue(): string
    {
        return Stringify::value($this->toFormattedValue());
    }

    protected function resolveRawValue(): string
    {
        return Stringify::value($this->toFormattedValue());
    }

    protected function resolvePreview(): Renderable|string
    {
        return Stringify::value($this->toFormattedValue());
    }
}
