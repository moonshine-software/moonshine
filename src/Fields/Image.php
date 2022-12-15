<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Leeto\MoonShine\Contracts\Fields\Fileable;
use Leeto\MoonShine\Traits\Fields\CanBeMultiple;
use Leeto\MoonShine\Traits\Fields\FileTrait;

class Image extends Field implements Fileable
{
    use CanBeMultiple;
    use FileTrait;

    public static string $view = 'moonshine::fields.image';

    public static string $type = 'file';

    public function indexViewValue(Model $item, bool $container = true): mixed
    {
        if ($item->{$this->field()} == '') {
            return '';
        }

        if ($this->isMultiple()) {
            $values = collect($item->{$this->field()})
                ->map(fn($value) => "'".Storage::url($this->unPrefixedValue($value))."'")->implode(',');

            return view('moonshine::shared.carousel', [
                'values' => $values
            ]);
        }

        return view('moonshine::fields.shared.thumbnail', [
            'value' => Storage::url($this->unPrefixedValue($item->{$this->field()})),
        ]);
    }
}
