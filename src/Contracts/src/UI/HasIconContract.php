<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use MoonShine\Support\Enums\Color;

interface HasIconContract
{
    public function icon(string $icon, bool $custom = false, ?string $path = null): static;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function getIcon(
        ?int $size = null,
        Color|string $color = '',
        array $attributes = []
    ): string;

    public function isCustomIcon(): bool;

    public function getIconPath(): ?string;

    public function getIconValue(): string;
}
