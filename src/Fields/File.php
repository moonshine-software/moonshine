<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\Fileable;
use Leeto\MoonShine\Contracts\Fields\RemovableContract;
use Leeto\MoonShine\Traits\Fields\CanBeMultiple;
use Leeto\MoonShine\Traits\Fields\FileTrait;
use Leeto\MoonShine\Traits\Removable;

class File extends Field implements Fileable, RemovableContract
{
    use CanBeMultiple;
    use FileTrait;
    use Removable;

    protected static string $view = 'moonshine::fields.file';

    protected string $type = 'file';

    protected string $accept = '*/*';

    protected array $attributes = [
        'type',
        'accept',
        'required',
        'disabled',
    ];

    public function accept(string $value): static
    {
        $this->accept = $value;

        return $this;
    }

    public function indexViewValue(Model $item, bool $container = true): string
    {
        if (! $item->{$this->field()}) {
            return '';
        }

        $files = $this->isMultiple()
            ? collect($item->{$this->field()})
                ->map(fn ($value) => $this->path($value))
                ->toArray()
            : [$this->path($item->{$this->field()})];

        return view('moonshine::components.files', [
            'files' => $files,
            'download' => $this->canDownload(),
        ])->render();
    }
}
