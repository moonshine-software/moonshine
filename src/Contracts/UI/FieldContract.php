<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\Core\TypeCasts\CastedDataContract;

interface FieldContract
{
    public function getColumn(): string;

    public function getLabel(): string;

    public function getValue(bool $withOld = true): mixed;

    public function getRequestValue(int|string|null $index = null): mixed;

    public function fill(mixed $value = null, ?CastedDataContract $casted = null, int $index = 0): static;

    public function toRawValue(): mixed;

    public function toValue(bool $withDefault = true): mixed;

    public function toFormattedValue(): mixed;

    public function getData(): ?CastedDataContract;

    public function preview(): Renderable|string;
}
