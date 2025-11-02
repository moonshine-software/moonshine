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
            ->set('ms-layout-body-bg-color', $value);
    }

    public function pageBackground(string $value): static
    {
        return $this
            ->set('ms-layout-page-bg-color', $value);
    }

    public function borders(string $value): static
    {
        return $this
            ->set('base.stroke', $value);
    }

    public function dividers(string $value): static
    {
        return $this
            ->set('ms-hr-divider-border-color', $value)
            ->set('ms-divider-line-bg-color', $value)
            ->set('ms-divider-color', $value);
    }

    // Buttons
    public function button(string $value): static
    {
        return $this
            ->set('ms-btn-bg-color', $value);
    }

    public function buttonText(string $value): static
    {
        return $this
            ->set('ms-btn-color', $value);
    }

    public function buttonHover(string $value): static
    {
        return $this
            ->set('ms-btn-hover-bg-color', $value);
    }

    public function buttonHoverText(string $value): static
    {
        return $this
            ->set('ms-btn-hover-color', $value);
    }

    // Alerts
    public function alert(string $value): static
    {
        return $this
            ->set('ms-alert-bg-color', $value);
    }

    public function alertText(string $value): static
    {
        return $this
            ->set('ms-alert-color', $value);
    }

    // Badges
    public function badge(string $value): static
    {
        return $this
            ->set('ms-badge-bg-color', $value);
    }

    public function badgeText(string $value): static
    {
        return $this
            ->set('ms-badge-color', $value);
    }

    // Collapse
    public function collapse(string $value): static
    {
        return $this
            ->set('ms-accordion-item-bg-color', $value);
    }

    public function collapseText(string $value): static
    {
        return $this
            ->set('ms-accordion-btn-color', $value)
            ->set('ms-accordion-item-color', $value);
    }

    public function collapseOpen(string $value): static
    {
        return $this
            ->set('ms-accordion-item-opened-bg-color', $value);
    }

    public function collapseOpenText(string $value): static
    {
        return $this
            ->set('ms-accordion-btn-active-color', $value)
            ->set('ms-accordion-item-opened-color', $value);
    }

    // Popovers
    public function popover(string $value): static
    {
        return $this
            ->set('ms-popover-border-color', $value)
            ->set('ms-popover-bg-color', $value);
    }

    public function popoverText(string $value): static
    {
        return $this
            ->set('ms-popover-color', $value);
    }

    // Progress bars
    public function progress(string $value): static
    {
        return $this
            ->set('ms-progress-bar-bg-color', $value)
            ->set('ms-radial-progress-track-color', $value);
    }

    public function progressText(string $value): static
    {
        return $this
            ->set('ms-progress-bar-color', $value)
            ->set('ms-radial-progress-color', $value);
    }

    // Modal
    public function modal(string $value): static
    {
        return $this
            ->set('ms-modal-content-bg-color', $value);
    }

    public function modalText(string $value): static
    {
        return $this
            ->set('ms-modal-content-color', $value);
    }

    // Offcanvas
    public function offcanvas(string $value): static
    {
        return $this
            ->set('ms-offcanvas-bg-color', $value);
    }

    public function offcanvasText(string $value): static
    {
        return $this
            ->set('ms-offcanvas-color', $value);
    }

    // Box
    public function box(string $value): static
    {
        return $this
            ->set('ms-box-bg-color', $value);
    }

    public function boxText(string $value): static
    {
        return $this
            ->set('ms-box-color', $value);
    }

    public function boxDark(string $value): static
    {
        return $this
            ->set('ms-box-dark-bg-color', $value);
    }

    public function boxDarkText(string $value): static
    {
        return $this
            ->set('ms-box-dark-color', $value);
    }

    // Cards
    public function card(string $value): static
    {
        return $this
            ->set('ms-card-bg-color', $value);
    }

    public function cardText(string $value): static
    {
        return $this
            ->set('ms-card-color', $value);
    }

    // Forms
    public function formDefault(string $value): static
    {
        return $this
            ->set('ms-form-default-bg-color', $value);
    }

    public function formDefaultText(string $value): static
    {
        return $this
            ->set('ms-form-default-color', $value);
    }

    public function formFocus(string $value): static
    {
        return $this
            ->set('ms-form-focus-border-color', $value)
            ->set('ms-form-focus-ring-color', $value);
    }

    public function formDisabled(string $value): static
    {
        return $this
            ->set('ms-form-disabled-bg-color', $value);
    }

    public function formDisabledText(string $value): static
    {
        return $this
            ->set('ms-form-disabled-color', $value);
    }

    public function formReadOnly(string $value): static
    {
        return $this
            ->set('ms-form-readonly-bg-color', $value);
    }

    public function formReadOnlyText(string $value): static
    {
        return $this
            ->set('ms-form-readonly-color', $value);
    }

    public function formExpansion(string $value): static
    {
        return $this
            ->set('ms-form-expansion-bg-color', $value);
    }

    public function formExpansionText(string $value): static
    {
        return $this
            ->set('ms-form-expansion-color', $value);
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
                ->implode(static fn (string $value, string $name): string => "--$name:$value;", PHP_EOL);
        };

        return <<<HTML
        <style>
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
