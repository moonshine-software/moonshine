<?php

declare(strict_types=1);

namespace MoonShine\Contracts\ColorManager;

interface PaletteContract
{
    public function getDescription(): string;

    /**
     * @return array<string, string|array<string|int, string>>
     */
    public function getColors(): array;

    /**
     * @return array<string, string|array<string|int, string>>
     */
    public function getDarkColors(): array;
}
