<?php

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Leeto\MoonShine\Contracts\Fields\FileFieldContract;
use Leeto\MoonShine\Traits\Fields\FileTrait;

class File extends BaseField implements FileFieldContract
{
    use FileTrait;

    protected static string $view = 'file';

    protected static string $type = 'file';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        if($this->isMultiple()) {
            return collect($item->{$this->field()})
                ->map(fn ($value, $index) => view('moonshine::fields.shared.file', [
                    'value' => Storage::url($value),
                    'index' => $index+1,
                    'canDownload' => $this->canDownload(),
                ])->render())->implode('');
        }

        return view(
            'moonshine::fields.shared.file', [
                'value' => parent::indexViewValue($item),
                'canDownload' => $this->canDownload(),
        ]);
    }
}