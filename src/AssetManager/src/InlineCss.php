<?php

declare(strict_types=1);

namespace MoonShine\AssetManager;

use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithComponentAttributes;

/**
 * @method static static make(string $content)
 */
final class InlineCss implements AssetElementContract
{
    use Makeable;
    use WithComponentAttributes;

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
            <style {$this->getAttributes()}>{$this->getContent()}</style>
        HTML;
    }

    public function __toString(): string
    {
        return $this->getContent();
    }
}
