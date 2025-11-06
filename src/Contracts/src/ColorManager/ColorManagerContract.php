<?php

declare(strict_types=1);

namespace MoonShine\Contracts\ColorManager;

use Illuminate\Contracts\Support\Htmlable;

interface ColorManagerContract extends Htmlable
{
    public function palette(PaletteContract $palette): self;

    public function get(string $name, null|int|string $shade = null, bool $dark = false, bool $hex = true): string;

    /**
     * @return array<string, string>
     */
    public function getAll(bool $dark = false): array;

    /**
     * @param  string|array<string|int, string>  $value
     *
     */
    public function set(string $name, string|array $value, bool $dark = false, bool $everything = false): static;

    /**
     * @param  string|array<string|int, string>  $value
     *
     */
    public function setEverything(string $name, string|array $value): static;

    /**
     * @api
     * @param array<string, string|array<string|int, string>> $colors
     */
    public function bulkAssign(array $colors, bool $dark = false, bool $everything = false): static;
}
