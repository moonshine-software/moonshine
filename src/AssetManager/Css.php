<?php

declare(strict_types=1);

namespace MoonShine\AssetManager;

use MoonShine\AssetManager\Contracts\AssetElement;
use MoonShine\AssetManager\Contracts\HasVersion;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithComponentAttributes;
use MoonShine\Support\Traits\WithVersion;

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
            return moonshineAssets()->getAsset($this->link) . "v={$this->getVersion()}";
        }

        return moonshineAssets()->getAsset($this->link);
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
