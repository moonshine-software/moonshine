<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

interface HasHintContract
{
    public function escapeHint(bool $escape = true): static;

    public function unescapeHint(): static;

    public function isEscapeHint(): bool;

    public function hint(string $hint): static;

    public function getHint(): string;
}
