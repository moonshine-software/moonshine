<?php

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\FileContract;
use Leeto\MoonShine\Traits\Fields\FileTrait;
use Illuminate\Support\Facades\Storage;

class Image extends Field implements FileContract
{
    use FileTrait;

    public static string $view = 'image';

    public static string $type = 'file';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        if($this->isMultiple()) {
            $values = collect($item->{$this->field()})
                ->map(fn ($value) => "'".Storage::url($value)."'")->implode(',');

            return view('moonshine::shared.carousel', [
                'values' => $values
            ]);
        }

        return view(
            'moonshine::fields.shared.thumbnail', [
                'value' => $item->{$this->field()},
        ]);
    }
}