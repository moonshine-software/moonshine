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

    protected string $itemView = 'moonshine::ui.file';

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

    protected function prepareForView(): array
    {
        if (! $this->value()) {
            return [];
        }

        return $this->isMultiple()
            ? collect($this->value())
                ->map(fn ($value): string => $this->pathWithDir($value))
                ->toArray()
            : [$this->pathWithDir($this->value())];
    }

    protected function itemView(): string
    {
        return $this->itemView;
    }

    protected function resolvePreview(): string
    {
        $values = $this->prepareForView();

        if($this->isRawMode()) {
            return implode(';', array_filter($values));
        }

        return collect($values)->implode(
            fn (string $value): string => view($this->itemView(), [
                'value' => $value,
            ])->render()
        );
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
