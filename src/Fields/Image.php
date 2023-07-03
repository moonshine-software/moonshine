<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Image extends File
{
    protected static string $view = 'moonshine::fields.image';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        if (! $item->{$this->field()}) {
            return '';
        }

        $files = $this->isMultiple()
            ? collect($item->{$this->field()})
                ->map(fn ($value): string => $this->pathWithDir($value ?? ''))
                ->toArray()
            : [$this->pathWithDir($item->{$this->field()})];

        if (! $container) {
            return implode(';', array_filter($files));
        }

        $viewData = $this->isMultiple()
            ? ['values' => $files]
            : ['value' => Arr::first($files)];

        return view('moonshine::ui.image', $viewData)->render();
    }
}
