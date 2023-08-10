<?php

declare(strict_types=1);

namespace MoonShine\Fields;

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

    protected string $view = 'moonshine::fields.file';

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

    protected function prepareForView(): string
    {
        if (! $this->value()) {
            return '';
        }

        $files = $this->isMultiple()
            ? collect($this->value())
                ->map(fn ($value): string => $this->pathWithDir($value))
                ->toArray()
            : [$this->pathWithDir($this->value())];

        return implode(';', array_filter($files));
    }

    protected function resolvePreview(): string
    {
        return view('moonshine::ui.file', [
            'value' => $this->prepareForView(),
        ])->render();
    }

    protected function resolveAfterDestroy(mixed $data): void
    {
        if (! $this->isDeleteFiles()) {
            return;
        }

        if ($this->isMultiple()) {
            foreach ($this->toValue() as $value) {
                $this->deleteFile($value);
            }
        } elseif (! empty($this->toValue())) {
            $this->deleteFile($this->toValue());
        }

        $this->deleteDir();
    }
}
