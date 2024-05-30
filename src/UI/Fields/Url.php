<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Illuminate\Contracts\View\View;
use MoonShine\UI\Components\Url as UrlComponent;

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
