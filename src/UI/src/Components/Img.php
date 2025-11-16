<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\Support\Collection;
use MoonShine\UI\Components\MoonShineComponent;

/**
 * @method static static make(string $src)
 */
final class Img extends MoonShineComponent
{
    protected string $view = 'moonshine::components.img';

    protected ?string $alt = null;

    protected ?int $width = null;

    protected ?int $height = null;

    protected ?string $loading = null;

    protected ?string $decoded = null;

    protected ?string $srcset = null;

    protected ?string $sizes = null;

    protected function __construct(protected string $src)
    {
        parent::__construct();
    }

    /**
     * @param string $alt
     * @return static
     */
    public function alt(string $alt): static
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * @param integer $widht
     * @param integer|null $height
     * @return static
     */
    public function size(int $widht, ?int $height = null): static
    {
        $this->width = $this->height = $widht;

        if (!is_null($height)) {
            $this->height = $height;
        }

        return $this;
    }

    /**
     * @param integer $width
     * @return static
     */
    public function width(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @param integer $height
     * @return static
     */
    public function height(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return static
     */
    public function rounded(): static
    {
        $this->style(['border-radius: 50%']);

        return $this;
    }

    /**
     * @return static
     */
    public function eagerLoaded(): static
    {
        $this->loading = 'eager';

        return $this;
    }

    /**
     * @return static
     */
    public function lazyLoaded(): static
    {
        $this->loading = 'lazy';

        return $this;
    }

    /**
     * @return static
     */
    public function autoDecoded(): static
    {
        $this->decoded = 'auto';

        return $this;
    }

    /**
     * @return static
     */
    public function asyncDecoded(): static
    {
        $this->decoded = 'async';

        return $this;
    }

    /**
     * @return static
     */
    public function syncDecoded(): static
    {
        $this->decoded = 'sync';

        return $this;
    }

    /**
     * @param array<string, string> $sources
     * @return static
     */
    public function srcset(array $sources): static
    {
        $this->srcset = Collection::make($sources)
            ->map(fn ($value, $key): string => "{$value} {$key}")
            ->join(', ');

        return $this;
    }

    /*
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'src' => $this->src,
            'alt' => $this->alt,
            'width' => $this->width,
            'height' => $this->height,
            'loading' => $this->loading,
            'decoded' => $this->decoded,
            'srcset' => $this->srcset,
            'sizes' => $this->sizes,
        ];
    }
}
