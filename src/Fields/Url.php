<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Components\Url as UrlComponent;

class Url extends Text
{
    protected string $type = 'url';

    protected ?Closure $titleCallback = null;

    public function title(Closure $callback): static
    {
        $this->titleCallback = $callback;

        return $this;
    }

    protected function resolvePreview(): View|string
    {
        $value = parent::resolvePreview();

        if ($this->isRawMode()) {
            return $value;
        }

        if ($value === '0' || $value === '') {
            return '';
        }

        return UrlComponent::make(
            href: $value,
            value: is_null($this->titleCallback)
                ? $value
                : (string) value($this->titleCallback, $value, $this),
            blank: $this->isLinkBlank()
        )->render();
    }
}
