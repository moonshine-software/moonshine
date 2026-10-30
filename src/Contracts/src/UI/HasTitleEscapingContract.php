<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

/**
 * Kept separate from ModalContract and OffCanvasContract so existing implementations stay compatible.
 */
interface HasTitleEscapingContract
{
    public function escapeTitle(bool $escape = true): static;

    public function unescapeTitle(): static;

    public function isEscapeTitle(): bool;
}
