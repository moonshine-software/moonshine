<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Illuminate\Contracts\Support\Htmlable;

/**
 * Kept separate from HasLabelContract so existing implementations stay compatible.
 */
interface HasLabelEscapingContract
{
    public function escapeLabel(bool $escape = true): static;

    public function unescapeLabel(): static;

    public function isEscapeLabel(): bool;

    public function hasLabelEscapingOverride(): bool;

    public function getLabelHtml(): Htmlable;
}
