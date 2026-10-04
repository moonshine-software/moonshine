<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

interface HasLabelContract
{
    public function escapeLabel(bool $escape = true): static;

    public function unescapeLabel(): static;

    public function isEscapeLabel(): bool;

    public function getLabelHtml(): Htmlable;

    public function hasLabel(): bool;

    public function getLabel(): string;

    public function setLabel(Closure|string $label): static;

    public function translatable(string $key = ''): static;
}
