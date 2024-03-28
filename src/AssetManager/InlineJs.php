<?php

declare(strict_types=1);

namespace MoonShine\AssetManager;

use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithComponentAttributes;
use MoonShine\Traits\WithVersion;

final class InlineJs implements AssetElement
{
    use Makeable;
    use WithComponentAttributes;
    use WithVersion;

    public function __construct(
        private readonly string $content,
    ) {
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
