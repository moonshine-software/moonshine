<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\Fileable;
use Leeto\MoonShine\Traits\Fields\CanBeMultiple;
use Leeto\MoonShine\Traits\Fields\FileTrait;
use Illuminate\Support\Facades\Storage;

class Image extends Field implements Fileable
{
    use FileTrait, CanBeMultiple;

    public static string $view = 'image';

    public static string $type = 'file';

    public function indexViewValue(Model $item, bool $container = true): mixed
    {
        if ($item->{$this->field()} == '') {
            return '';
        }

        if ($this->isMultiple()) {
            $values = collect($item->{$this->field()})
                ->map(fn($value) => "'".Storage::url($value)."'")->implode(',');

            return view('moonshine::shared.carousel', [
                'values' => $values
            ]);
        }

        return view('moonshine::fields.shared.thumbnail', [
            'value' => $item->{$this->field()},
        ]);
    }
}
