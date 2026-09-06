<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Illuminate\Contracts\Support\Renderable;
use MoonShine\UI\Components\Thumbnails;

class Image extends File
{
    protected string $view = 'moonshine::fields.image';

    protected function resolveRawValue(): mixed
    {
        $values = $this->getFullPathValues();

        return implode(';', array_filter($values));
    }

    protected function resolvePreview(): Renderable|string
    {
        return (string) Thumbnails::make(
            $this->isMultiple()
                ? array_values($this->getFiles()->all())
                : $this->getFiles()->first(),
        );
    }
}
