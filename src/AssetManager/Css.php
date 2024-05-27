<?php

declare(strict_types=1);

namespace MoonShine\AssetManager;

use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithVersion;
use MoonShine\UI\Components\MoonShineComponentAttributeBag;
use MoonShine\UI\Traits\Components\WithComponentAttributes;

final class Css implements AssetElement, HasVersion
{
    use Makeable;
    use WithVersion;
    use WithComponentAttributes;

    public function __construct(
        private readonly string $link,
    ) {

        $this->attributes = new MoonShineComponentAttributeBag([
            'href' => $this->getLink(),
            'rel' => 'stylesheet',
        ]);
    }

    public function defer(): self
    {
        $this->customAttributes([
            'defer' => '',
        ]);

        return $this;
    }

    public function getLink(): string
    {
        if (! is_null($this->getVersion())) {
            return asset($this->link) . "v={$this->getVersion()}";
        }

        return asset($this->link);
    }

    public function toHtml(): string
    {
        return <<<HTML
            <link {$this->attributes()}>
        HTML;
    }

    public function __toString(): string
    {
        return $this->link;
    }
}
