<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\Fileable;
use MoonShine\Contracts\Fields\RemovableContract;
use MoonShine\Traits\Fields\CanBeMultiple;
use MoonShine\Traits\Fields\FileDeletable;
use MoonShine\Traits\Fields\FileTrait;
use MoonShine\Traits\Removable;

class File extends Field implements Fileable, RemovableContract
{
    use CanBeMultiple;
    use FileTrait;
    use FileDeletable;
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
                ->map(fn ($value) => $this->pathWithDir($value))
                ->toArray()
            : [$this->pathWithDir($item->{$this->field()})];

        return view('moonshine::components.files', [
            'files' => $files,
            'download' => $this->canDownload(),
        ])->render();
    }

    public function afterDelete(Model $item): void
    {
        if (!$this->isDeleteFiles()) {
            return;
        }

        if ($this->isMultiple()) {
            foreach ($item->{$this->field()} as $value) {
                $this->deleteFile($value);
            }
        } elseif(!empty($item->{$this->field()})) {
            $this->deleteFile($item->{$this->field()});
        }

        $this->deleteDir();
    }
}
