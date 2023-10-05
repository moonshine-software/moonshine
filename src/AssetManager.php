<?php

declare(strict_types=1);

namespace MoonShine;

use Composer\InstalledVersions;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Support\Colors;

/**
 * @todo pull theme options into a separate class
 */
class AssetManager
{
    use Conditionable;

    private array $assets = [];

    private array $colors = [
        'primary' => '120, 67, 233',
        'secondary' => '236, 65, 118',
        'body' => '27, 37, 59',
        'dark' => [
            'DEFAULT' => '30, 31, 67',
            50 => '83, 103, 132',
            100 => '74, 90, 121',
            200 => '65, 81, 114',
            300 => '53, 69, 103',
            400 => '48, 61, 93',
            500 => '41, 53, 82',
            600 => '40, 51, 78',
            700 => '39, 45, 69',
            800 => '27, 37, 59',
            900 => '15, 23, 42',
        ],

        'success-bg' => '0, 170, 0',
        'success-text' => '255, 255, 255',
        'warning-bg' => '255, 220, 42',
        'warning-text' => '139, 116, 0',
        'error-bg' => '224, 45, 45',
        'error-text' => '255, 255, 255',
        'info-bg' => '0, 121, 255',
        'info-text' => '255, 255, 255',
    ];

    private array $darkColors = [
        'body' => '27, 37, 59',
        'success-bg' => '17, 157, 17',
        'success-text' => '178, 255, 178',
        'warning-bg' => '225, 169, 0',
        'warning-text' => '255, 255, 199',
        'error-bg' => '190, 10, 10',
        'error-text' => '255, 197, 197',
        'info-bg' => '38, 93, 205',
        'info-text' => '179, 220, 255',
    ];

    private string $mainJs = '/vendor/moonshine/js/moonshine.js';

    private ?string $mainCss = null;

    private string $vendorBuildDirectory = 'vendor/moonshine';
    private array $vendorStyleEntryPoints = ['resources/js/app.css'];
    private array $vendorScriptEntryPoints = ['resources/js/app.js'];

    public function mainCss(string $path): self
    {
        $this->mainCss = $path;

        return $this;
    }

    private function getMainCss(): ?string
    {
        return $this->mainCss;
    }

    private function getMainJs(): string
    {
        return $this->mainJs;
    }

    public function add(string|array $assets): void
    {
        $this->assets = array_unique(
            array_merge(
                $this->assets,
                is_array($assets) ? $assets : [$assets]
            )
        );
    }

    public function getAssets(): array
    {
        return $this->assets;
    }

    public function js(): string
    {
        $vendorScripts = $this->buildVendorAssets($this->vendorScriptEntryPoints);

        $customScripts = collect($this->assets)
            ->filter(
                fn ($asset): int|bool => str_contains((string) $asset, '.js')
            )
            ->map(
                fn ($asset): string => "<script defer src='" . asset(
                    $asset
                ) . (str_contains((string) $asset, '?') ? '&' : '?') . "v={$this->getVersion()}'></script>"
            )->implode(PHP_EOL);

        return $vendorScripts.$customScripts;
    }

    public function css(): string
    {
        $vendorStyles = is_null($this->mainCss)
            ? $this->buildVendorAssets($this->vendorStyleEntryPoints)
            : '';

        $customStyles = collect($this->assets)
            ->filter(
                fn ($asset): int|bool => str_contains((string) $asset, '.css')
            )
            ->when(!is_null($this->mainCss), fn (Collection $assets) => $assets->push($this->mainCss))
            ->map(
                fn ($asset): string => "<link href='" . asset(
                    $asset
                ) . (str_contains((string) $asset, '?') ? '&' : '?') . "v={$this->getVersion()}' rel='stylesheet'>"
            )->implode(PHP_EOL);

        return $vendorStyles.$customStyles;
    }

    private function buildVendorAssets(array $entryPoints): string
    {
        return app(Vite::class)
            ->useBuildDirectory($this->vendorBuildDirectory)
            ->useHotFile(
                InstalledVersions::getInstallPath('moonshine/moonshine').'/public/hot'
            )
            ->withEntryPoints($entryPoints)
            ->toHtml();
    }

    public function colors(array $colors): static
    {
        foreach ($colors as $name => $color) {
            $this->colors[$name] = $color;
        }

        return $this;
    }

    public function darkColors(array $colors): static
    {
        foreach ($colors as $name => $color) {
            $this->darkColors[$name] = $color;
        }

        return $this;
    }

    public function getColor(string $name, ?int $shade = null, bool $dark = false, bool $hex = true): string
    {
        $data = $dark ? $this->darkColors : $this->colors;
        $value = $data[$name];
        $value = is_null($shade)
            ? $value
            : $value[$shade];

        return $hex ? Colors::toHEX($value) : $value;
    }

    public function getColors(bool $dark = false): array
    {
        $colors = [];
        $data = $dark ? $this->darkColors : $this->colors;

        foreach ($data as $name => $shades) {
            if (! is_array($shades)) {
                $colors[$name] = Colors::toRGB($shades);
            } else {
                foreach ($shades as $shade => $color) {
                    $colors["$name-$shade"] = Colors::toRGB($color);
                }
            }
        }

        return $colors;
    }

    public function getVersion(): string
    {
        return InstalledVersions::getVersion('moonshine/moonshine');
    }
}
