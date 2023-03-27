<?php

namespace Leeto\MoonShine\Fields\Spatie;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Traits\Fields\CanBeMultiple;

class MediaLibrary extends Field
{
    use CanBeMultiple;

    public static string $view = 'moonshine::fields.image';

    public static string $type = 'file';

    public function save(Model $item): Model
    {
        return $item;
    }

    public function afterSave(Model $item): void
    {
        if ($this->requestValue()) {
            if ($this->isMultiple()) {
                $item->clearMediaCollection($this->field());
                foreach ($this->requestValue() as $file) {
                    $item->addMedia($file)
                        ->preservingOriginal()
                        ->toMediaCollection($this->field());
                }
            } else {
                if ($media = $item->getFirstMedia($this->field())) {
                    $media->delete();
                }
                $item->addMedia($this->requestValue())
                    ->preservingOriginal()
                    ->toMediaCollection($this->field());
            }
        }
    }

    public function indexViewValue(Model $item, bool $container = true): mixed
    {
        if ($this->isMultiple()) {
            return view('moonshine::ui.image', [
                'values' => $item->getMedia($this->field())
                    ->map(fn ($value) => $value->getUrl())
                    ->toArray(),
            ]);
        }

        $url = $item->getFirstMediaUrl($this->field());

        if (empty($url)) {
            return null;
        }

        return view('moonshine::ui.image', [
            'value' => $url,
        ]);
    }

    public function formViewValue(Model $item): mixed
    {
        if ($this->isMultiple()) {
            return $item->getMedia($this->field())
                ->map(fn ($value) => $value->getUrl())->toArray();
        }

        return $item->getFirstMediaUrl($this->field());
    }
    
    
    public function path(string $value): string
    {
        return '';
    }
}
