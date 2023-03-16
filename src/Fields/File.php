<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\Fileable;
use Leeto\MoonShine\Traits\Fields\CanBeMultiple;
use Leeto\MoonShine\Traits\Fields\FileTrait;

class File extends Field implements Fileable
{
    use CanBeMultiple;
    use FileTrait;

    protected static string $view = 'moonshine::fields.file';

    protected static string $type = 'file';

    protected string $accept = '*/*';

    protected array $attributes = [
        'accept',
    ];

    public function accept(string $value): static
    {
        $this->accept = $value;

        return $this;
    }

    public function indexViewValue(Model $item, bool $container = true): mixed
    {
        if (!$item->{$this->field()}) {
            return '';
        }

        if ($this->isMultiple()) {
            return collect($item->{$this->field()})
                ->map(fn ($value, $index) => view('moonshine::ui.file', [
                    'value' => $this->path($value),
                    'download' => $this->canDownload(),
                ])->render())->implode('');
        }

        return view('moonshine::ui.file', [
            'value' => $this->path($item->{$this->field()}),
            'download' => $this->canDownload(),
        ]);
    }
}
