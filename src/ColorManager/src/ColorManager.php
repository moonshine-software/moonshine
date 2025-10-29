<?php

declare(strict_types=1);

namespace MoonShine\ColorManager;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\ColorManager\Palettes\DefaultPalette;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\ColorManager\PaletteContract;

/**
 * @method self primary(string $value, ?int $shade = null, bool $dark = false)
 * @method self secondary(string $value, ?int $shade = null, bool $dark = false)
 * @method self body(string $value, ?int $shade = null, bool $dark = false)
 * @method self base(string $value, int|string|null $shade = null, bool $dark = false)
 * @method self successBg(string $value, ?int $shade = null, bool $dark = false)
 * @method self successText(string $value, ?int $shade = null, bool $dark = false)
 * @method self warningBg(string $value, ?int $shade = null, bool $dark = false)
 * @method self warningText(string $value, ?int $shade = null, bool $dark = false)
 * @method self error(string $value, ?int $shade = null, bool $dark = false)
 * @method self errorBg(string $value, ?int $shade = null, bool $dark = false)
 * @method self errorText(string $value, ?int $shade = null, bool $dark = false)
 * @method self infoBg(string $value, ?int $shade = null, bool $dark = false)
 * @method self infoText(string $value, ?int $shade = null, bool $dark = false)
 */
final class ColorManager implements ColorManagerContract
{
    use Conditionable;

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
        $palette ??= new DefaultPalette();

        $this->colors = $palette->getColors();
        $this->darkColors = $palette->getDarkColors();
    }

    public function palette(PaletteContract $palette): self
    {
        $this->colors = $palette->getColors();
        $this->darkColors = $palette->getDarkColors();

        return $this;
    }

    public function background(string $value): static
    {
        return $this
            ->set('body', $value)
            ->set('base.800', $value)
            ->set('body', $value, dark: true);
    }

    public function tableRow(string $value): static
    {
        return $this
            ->set('base.600', $value);
    }

    public function borders(string $value): static
    {
        return $this
            ->set('base.300', $value);
    }

    public function dropdowns(string $value): static
    {
        return $this
            ->set('base.400', $value);
    }

    public function buttons(string $value): static
    {
        return $this
            ->set('base.50', $value)
            ->set('base.500', $value)
            ->dropdowns($value);
    }

    public function dividers(string $value): static
    {
        return $this
            ->set('base.100', $value)
            ->set('base.200', $value);
    }

    public function content(string $value): static
    {
        return $this
            ->set('base.700', $value)
            ->set('base.900', $value);
    }

    /**
     * @param  string|array<string|int, string>  $value
     *
     */
    public function set(string $name, string|array $value, bool $dark = false): static
    {
        /** @phpstan-ignore-next-line */
        data_set($this->{$dark ? 'darkColors' : 'colors'}, $name, $value);

        return $this;
    }

    /**
     * @api
     * @param array<string, string|array<string|int, string>> $colors
     */
    public function bulkAssign(array $colors, bool $dark = false): static
    {
        foreach ($colors as $name => $color) {
            $this->set($name, $color, $dark);
        }

        return $this;
    }

    public function get(string $name, ?int $shade = null, bool $dark = false, bool $hex = true): string
    {
        $data = $dark ? $this->darkColors : $this->colors;
        $value = $data[$name];
        $value = \is_null($shade)
            ? $value
            : $value[$shade];

        $hexValue = \is_array($value) ? $value['DEFAULT'] : $value;

        return $hex ?
            ColorMutator::toHEX($hexValue)
            : $hexValue;
    }

    /**
     * @return array<string, string>
     */
    public function getAll(bool $dark = false): array
    {
        $colors = [];
        $data = $dark ? $this->darkColors : $this->colors;

        $formatRgb = static fn (string $rgb): string => str_replace(['rgb(', ')', 'oklch('], ['', ''], $rgb);

        foreach ($data as $name => $shades) {
            if (! \is_array($shades)) {
                $colors[$name] = $formatRgb(ColorMutator::toOKLCH($shades));
            } else {
                foreach ($shades as $shade => $color) {
                    $colors["$name-$shade"] = $formatRgb(ColorMutator::toOKLCH($color));
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
                ->implode(static fn (string $value, string $name): string => "--$name:$value;", PHP_EOL);
        };

        return <<<HTML
        <style>
            :root {
            {$values($this->getAll())}
            }
            :root.dark {
            {$values($this->getAll(dark: true))}
            }
        </style>
        HTML;
    }
}
