<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Support\Arr;

class Image extends File
{
    protected static string $view = 'moonshine::fields.image';

    public function preview(): string
    {
        if (! $this->value()) {
            return '';
        }

        $files = $this->isMultiple()
            ? collect($this->value())
                ->map(fn ($value): string => $this->pathWithDir($value ?? ''))
                ->toArray()
            : [$this->pathWithDir($this->value())];

        if (! false) { // $container
            return implode(';', array_filter($files));
        }

        $viewData = $this->isMultiple()
            ? ['values' => $files]
            : ['value' => Arr::first($files)];

        return view('moonshine::ui.image', $viewData)->render();
    }
}
