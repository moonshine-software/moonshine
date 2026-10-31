<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

/**
 * Kept separate from HasLinkContract so existing implementations stay compatible.
 */
interface HasLinkNameEscapingContract
{
    public function escapeLinkName(bool $escape = true): static;

    public function unescapeLinkName(): static;

    public function isEscapeLinkName(): bool;
}
