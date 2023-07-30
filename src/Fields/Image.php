<?php

declare(strict_types=1);

namespace MoonShine\Fields;

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

        return implode(';', array_filter($files));
    }
}
