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
 * @method self theme(string $value, int|string|null $shade = null, bool $dark = false)
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

    public const DEFAULT = [
        'primary' => '0.627 0.265 303.9',
        'primary-text' => '1 0 0',
        'secondary' => '0.746 0.16 232.661',
        'secondary-text' => '1 0 0',
        'body' => '0.98 0.0035 67.78',
        'theme' => [
            'body' => '0 0 0', // Default body text
            'stroke' => '0 0 0 / 10%', // Default border
            'default' => '1 0 0', // Default background
            50 => '0.99 0 0',
            100 => '0.98 0 0',
            200 => '0.97 0 0',
            300 => '0.96 0 0',
            400 => '0.95 0 0',
            500 => '0.94 0 0',
            600 => '0.93 0 0',
            700 => '0.92 0 0',
            800 => '0.91 0 0',
            900 => '0.90 0 0',
        ],
        'success-bg' => '0.639 0.218 142.495',
        'success-text' => '0.4676 0.1549 142.495',
        'warning-bg' => '0.8088 0.170358 75.3501',
        'warning-text' => '0.5 0.1031 76.1',
        'error-bg' => '0.589 0.214 26.855',
        'error-text' => '0.3706 0.145 26.855',
        'info-bg' => '0.601 0.219 257.63',
        'info-text' => '0.3471 0.1204 257.63',
    ];

    public const DARK = [
        'primary' => '0.606 0.25 292.717',
        'primary-text' => '1 0 0',
        'secondary' => '0.746 0.16 232.661',
        'secondary-text' => '1 0 0',
        'body' => '0.2 0.0168 274.32',
        'theme' => [
            'body' => '1 0 0', // Default body text
            'stroke' => '1 0 0 / 10%', // Default border
            'default' => '0.24 0.0168 274.32', // Default background
            50 => '0.255 0.017 274.32',
            100 => '0.27 0.017 274.32',
            200 => '0.285 0.018 274.32',
            300 => '0.30 0.019 274.32',
            400 => '0.315 0.020 274.32',
            500 => '0.33 0.021 274.32',
            600 => '0.345 0.022 274.32',
            700 => '0.36 0.023 274.32',
            800 => '0.375 0.024 274.32',
            900 => '0.39 0.025 274.32',
        ],
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
            ->set('theme.800', $value)
            ->set('body', $value, dark: true);
    }

    public function tableRow(string $value): static
    {
        return $this
            ->set('theme.600', $value);
    }

    public function borders(string $value): static
    {
        return $this
            ->set('theme.300', $value);
    }

    public function dropdowns(string $value): static
    {
        return $this
            ->set('theme.400', $value);
    }

    public function buttons(string $value): static
    {
        return $this
            ->set('theme.50', $value)
            ->set('theme.500', $value)
            ->dropdowns($value);
    }

    public function dividers(string $value): static
    {
        return $this
            ->set('theme.100', $value)
            ->set('theme.200', $value);
    }

    public function content(string $value): static
    {
        return $this
            ->set('theme.700', $value)
            ->set('theme.900', $value);
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
