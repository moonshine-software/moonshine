<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

/**
 * Escaping of beforeRender() and afterRender() output.
 * Kept separate from FieldContract so existing implementations stay compatible.
 */
interface HasRenderEscapingContract
{
    public function escapeBeforeRender(bool $escape = true): static;

    public function unescapeBeforeRender(): static;

    public function isEscapeBeforeRender(): bool;

    public function escapeAfterRender(bool $escape = true): static;

    public function unescapeAfterRender(): static;

    public function isEscapeAfterRender(): bool;
}
