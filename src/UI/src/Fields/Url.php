<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\Support\Enums\TextWrap;
use MoonShine\UI\Components\Link;

class Url extends Text
{
    protected string $type = 'url';

    protected ?Closure $titleCallback = null;

    protected bool $blank = false;

    protected ?TextWrap $textWrap = null;

    public function title(Closure $callback): static
    {
        $this->titleCallback = $callback;

        return $this;
    }

    public function blank(): static
    {
        $this->blank = true;

        return $this;
    }

    protected function resolvePreview(): Renderable|string
    {
        $value = $this->toFormattedValue() ?? '';

        $title = $this->isUnescape()
            ? $value
            : $this->escapeValue($value);

        if ($value === '0' || $value === '') {
            return '';
        }

        return Link::make(
            href: $value,
            label: \is_null($this->titleCallback)
                ? $title
                : (string) \call_user_func($this->titleCallback, $title, $this),
        )->when(
            $this->blank,
            fn (Link $ctx): Link => $ctx->blank()
        )->icon('link')->customAttributes($this->getAttributes()->except('type')->jsonSerialize())->render();
    }
}
