<?php

declare(strict_types=1);

namespace MoonShine\ColorManager;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\ColorManager\Palettes\PurplePalette;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;

final class ColorManager implements ColorManagerContract
{
    use Conditionable;
    use ColorShortcuts;

    /**
     * @var array<string, string|array<string|int, string>>
     */
    private array $colors;

    /**
     * @var array<string, string|array<string|int, string>>
     */
    private array $darkColors;

    public function __construct(?PaletteContract $palette = null)
    {
        $palette ??= new PurplePalette();

        $this->colors = $palette->getColors();
        $this->darkColors = $palette->getDarkColors();
    }

    public function palette(PaletteContract $palette): self
    {
        $this->colors = $palette->getColors();
        $this->darkColors = $palette->getDarkColors();

        return $this;
    }

    /**
     * @param  string|array<string|int, string>  $value
     *
     */
    public function set(string $name, string|array $value, bool $dark = false, bool $everything = false): static
    {
        if ($everything) {
            return $this->setEverything($name, $value);
        }

        /** @phpstan-ignore-next-line */
        data_set($this->{$dark ? 'darkColors' : 'colors'}, $name, $value);

        return $this;
    }

    /**
     * @param  string|array<string|int, string>  $value
     *
     */
    public function setEverything(string $name, string|array $value): static
    {
        $this->set($name, $value);
        $this->set($name, $value, dark: true);

        return $this;
    }

    /**
     * @api
     * @param array<string, string|array<string|int, string>> $colors
     */
    public function bulkAssign(array $colors, bool $dark = false, bool $everything = false): static
    {
        foreach ($colors as $name => $color) {
            $this->set($name, $color, $dark, $everything);
        }

        return $this;
    }

    public function get(string $name, null|int|string $shade = null, bool $dark = false, bool $hex = true): string
    {
        $data = $dark ? $this->darkColors : $this->colors;
        $value = $data[$name];

        if (! \is_null($shade) && \is_array($value)) {
            $value = $value[$shade];
        }

        $result = \is_array($value) ? $value['default'] : $value;

        return $hex ?
            ColorMutator::toHEX($result)
            : $result;
    }

    /**
     * @return array<string, string>
     */
    public function getAll(bool $dark = false): array
    {
        $colors = [];
        $data = $dark ? $this->darkColors : $this->colors;

        $formatted = static fn (string $value): string => "oklch(" . str_replace(['rgb(', ')', 'oklch('], ['', ''], $value) . ")";

        foreach ($data as $name => $shades) {
            if (! \is_array($shades)) {
                $colors[$name] = $formatted(ColorMutator::toOKLCH($shades));
            } else {
                foreach ($shades as $shade => $color) {
                    $colors["$name-$shade"] = $formatted(ColorMutator::toOKLCH($color));
                }
            }
        }

        return $colors;
    }

    /**
     * @param array{value: string, shade: int|string|null, dark: bool}|array{string, int|string|null, bool} $arguments
     */
    public function __call(string $name, array $arguments): static
    {
        $value = $arguments['value'] ?? $arguments[0] ?? '';
        $shade = $arguments['shade'] ?? $arguments[1] ?? false;
        $dark = $arguments['dark'] ?? $arguments[2] ?? false;

        $this->set(
            name: Str::of($name)
                ->kebab()
                ->when(
                    $shade,
                    static fn (Stringable $str) => $str->append(".$shade")
                )
                ->value(),
            value: $value,
            dark: $dark,
        );

        return $this;
    }

    public function toHtml(): string
    {
        $values = static function (array $data): string {
            /** @var Collection<string, string> $collection */
            $collection = new Collection($data);

            return $collection
                ->implode(static fn (string $value, string $name): string => "--ms-cm-$name: $value;", PHP_EOL);
        };

        return <<<HTML
        <style id="colors-definer">
            :root:not(.dark) {
            {$values($this->getAll())}
            }
            :root.dark {
            {$values($this->getAll(dark: true))}
            }
        </style>
        HTML;
    }
}
