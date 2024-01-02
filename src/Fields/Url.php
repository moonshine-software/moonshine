<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Contracts\View\View;
use MoonShine\Components\Url as UrlComponent;

class Url extends Text
{
    protected string $type = 'url';

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
            $value,
            $value,
            blank: $this->isLinkBlank()
        )->render();
    }
}
