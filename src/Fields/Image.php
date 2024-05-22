<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Contracts\View\View;
use MoonShine\Components\Thumbnails;

class Image extends File
{
    protected string $view = 'moonshine::fields.image';

    protected function resolvePreview(): View|string
    {
        $values = $this->getFullPathValues();

        if ($this->isRawMode()) {
            return implode(';', array_filter($values));
        }

        return Thumbnails::make(
            $this->isMultiple()
                ? $this->getFiles()->toArray()
                : $this->getFiles()->first(),
        )->render();
    }
}
