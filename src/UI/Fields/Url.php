<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\UI\Components\Url as UrlComponent;

class Url extends Text
{
    protected string $type = 'url';

    protected ?Closure $titleCallback = null;

    protected bool $blank = false;

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

        if ($this->isRawMode()) {
            return $value;
        }

        if ($value === '0' || $value === '') {
            return '';
        }

        return UrlComponent::make(
            href: $value,
            value: is_null($this->titleCallback)
                ? $title
                : (string) value($this->titleCallback, $title, $this),
            blank: $this->blank
        )->render();
    }
}
