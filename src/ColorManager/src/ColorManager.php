<?php

declare(strict_types=1);

namespace MoonShine\ColorManager;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\ColorManager\ColorManagerContract;

/**
 * @method self primary(string $value, ?int $shade = null, bool $dark = false)
 * @method self secondary(string $value, ?int $shade = null, bool $dark = false)
 * @method self body(string $value, ?int $shade = null, bool $dark = false)
 * @method self dark(string $value, int|string|null $shade = null, bool $dark = false)
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

    public const TEXT = '1 0 0';

    public const BG = '0.266 0.044 264.943';

    public const DEFAULT = [
        'primary' => '0.544 0.233 291.241',
        'secondary' => '0.639 0.209 6.961',
        'body' => self::BG,
        'dark' => [
            'DEFAULT' => '0.26 0.066 279.517',
            50 => '0.509 0.052 257.609', // search, toasts, progress bars
            100 => '0.467 0.054 263.268', // dividers
            200 => '0.436 0.059 264.202', // dividers
            300 => '0.393 0.062 264.473', // borders
            400 => '0.364 0.058 266.668', // dropdowns, buttons, pagination
            500 => '0.332 0.054 266.369', // buttons default bg
            600 => '0.324 0.051 266.679', // table row
            700 => '0.303 0.044 272.77', // background content
            800 => self::BG, // background sidebar
            900 => '0.208 0.04 265.755', // background
        ],

        'success-bg' => '0.639 0.218 142.495',
        'success-text' => self::TEXT,
        'warning-bg' => '0.898 0.177 96.726',
        'warning-text' => '0.5641 0.115857 95.1424',
        'error-bg' => '0.589 0.214 26.855',
        'error-text' => self::TEXT,
        'info-bg' => '0.601 0.219 257.63',
        'info-text' => self::TEXT,
    ];

    public const DARK = [
        'body' => self::BG,
        'success-bg' => '0.639 0.218 142.495',
        'success-text' => '0.9308 0.1279 144.46',
        'warning-bg' => '0.898 0.177 96.726',
        'warning-text' => '0.9865 0.0716 107.64',
        'error-bg' => '0.589 0.214 26.855',
        'error-text' => '0.8751 0.0665 18.51',
        'info-bg' => '0.601 0.219 257.63',
        'info-text' => '0.877 0.065 244.38',
    ];

    /**
     * @var array<string, string|array<string|int, string>>
     */
    private array $colors = self::DEFAULT;

    /**
     * @var array<string, string|array<string|int, string>>
     */
    private array $darkColors = self::DARK;

    public function background(string $value): static
    {
        return $this
            ->set('body', $value)
            ->set('dark.800', $value)
            ->set('body', $value, dark: true);
    }

    public function tableRow(string $value): static
    {
        return $this
            ->set('dark.600', $value);
    }

    public function borders(string $value): static
    {
        return $this
            ->set('dark.300', $value);
    }

    public function dropdowns(string $value): static
    {
        return $this
            ->set('dark.400', $value);
    }

    public function buttons(string $value): static
    {
        return $this
            ->set('dark.50', $value)
            ->set('dark.500', $value)
            ->dropdowns($value);
    }

    public function dividers(string $value): static
    {
        return $this
            ->set('dark.100', $value)
            ->set('dark.200', $value);
    }

    public function content(string $value): static
    {
        return $this
            ->set('dark.700', $value)
            ->set('dark.900', $value);
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
