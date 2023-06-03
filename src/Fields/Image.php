<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

class Image extends File
{
    protected static string $view = 'moonshine::fields.image';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        if (! $item->{$this->field()}) {
            return '';
        }

        if ($this->isMultiple()) {
            return view('moonshine::ui.image', [
                'values' => collect($item->{$this->field()})
                    ->map(
                        fn ($value): string => $this->pathWithDir($value ?? '')
                    )
                    ->toArray(),
            ])->render();
        }

        return view('moonshine::ui.image', [
            'value' => $this->pathWithDir($item->{$this->field()}),
        ])->render();
    }
}
