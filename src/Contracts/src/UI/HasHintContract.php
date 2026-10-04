<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Illuminate\Contracts\Support\Htmlable;

interface HasHintContract
{
    public function escapeHint(bool $escape = true): static;

    public function unescapeHint(): static;

    public function isEscapeHint(): bool;

    public function hint(string $hint): static;

    public function getHint(): string;

    public function getHintHtml(): Htmlable;
}
