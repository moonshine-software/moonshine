<?php

declare(strict_types=1);

namespace MoonShine\AssetManager;

use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithVersion;
use MoonShine\UI\Components\MoonShineComponentAttributeBag;
use MoonShine\UI\Traits\Components\WithComponentAttributes;

final class InlineJs implements AssetElement
{
    use Makeable;
    use WithComponentAttributes;
    use WithVersion;

    public function __construct(
        private readonly string $content,
    ) {
        $this->attributes = new MoonShineComponentAttributeBag();
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function toHtml(): string
    {
        return <<<HTML
            <script {$this->attributes()}>{$this->getContent()}</script>
        HTML;
    }

    public function __toString(): string
    {
        return $this->getContent();
    }
}
