<?php

declare(strict_types=1);

namespace MoonShine\AssetManager;

use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithComponentAttributes;

final class InlineCss implements AssetElement
{
    use Makeable;
    use WithComponentAttributes;

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
            <style {$this->attributes()}>{$this->getContent()}</style>
        HTML;
    }

    public function __toString(): string
    {
        return $this->getContent();
    }
}
