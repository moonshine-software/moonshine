<?php

declare(strict_types=1);

namespace MoonShine\AssetManager;

use MoonShine\AssetManager\Contracts\AssetElement;
use MoonShine\AssetManager\Contracts\HasVersion;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithComponentAttributes;
use MoonShine\Support\Traits\WithVersion;

final class Js implements AssetElement, HasVersion
{
    use Makeable;
    use WithVersion;
    use WithComponentAttributes;

    public function __construct(
        private readonly string $link,
    ) {
        $this->attributes = new MoonShineComponentAttributeBag([
            'src' => $this->getLink(),
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
            return moonshineAssets()->asset($this->link) . "v={$this->getVersion()}";
        }

        return moonshineAssets()->asset($this->link);
    }

    public function toHtml(): string
    {
        return <<<HTML
            <script {$this->attributes()}></script>
        HTML;
    }

    public function __toString(): string
    {
        return $this->link;
    }
}
