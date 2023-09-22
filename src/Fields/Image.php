<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;

class Image extends File
{
    protected string $view = 'moonshine::fields.image';

    protected function resolvePreview(): View|string
    {
        $values = $this->prepareForView();

        if ($this->isRawMode()) {
            return implode(';', array_filter($values));
        }

        return view(
            'moonshine::ui.image',
            $this->isMultiple() ? [
                'values' => $values,
            ] : ['value' => Arr::first($values)]
        );
    }
}
