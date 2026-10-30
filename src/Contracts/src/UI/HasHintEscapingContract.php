<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Illuminate\Contracts\Support\Htmlable;

/**
 * Kept separate from HasHintContract so existing implementations stay compatible.
 */
interface HasHintEscapingContract
{
    public function escapeHint(bool $escape = true): static;

    public function unescapeHint(): static;

    public function isEscapeHint(): bool;

    public function getHintHtml(): Htmlable;
}
