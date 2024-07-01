<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Illuminate\Contracts\Support\Renderable;
use MoonShine\UI\Components\Thumbnails;

class Image extends File
{
    protected string $view = 'moonshine::fields.image';

    protected function resolvePreview(): Renderable|string
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
