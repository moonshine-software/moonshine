<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Contracts\View\View;
use MoonShine\Components\Files;
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

    protected array $propertyAttributes = [
        'type',
        'accept',
    ];

    public function accept(string $value): static
    {
        $this->accept = $value;
        $this->attributes()->set('accept', $value);

        return $this;
    }

    protected function resolvePreview(): View|string
    {
        $values = $this->getFullPathValues();

        if ($this->isRawMode()) {
            return implode(';', array_filter($values));
        }

        return Files::make(
            $values,
            download: $this->canDownload(),
            names: $this->resolveNames(),
            itemAttributes: $this->resolveItemAttributes(),
        )->render();
    }

    protected function resolveAfterDestroy(mixed $data): mixed
    {
        if (! $this->isDeleteFiles() || blank($this->toValue())) {
            return $data;
        }

        collect($this->isMultiple() ? $this->toValue() : [$this->toValue()])
            ->each(fn ($file): bool => $this->deleteFile($file));

        $this->deleteDir();

        return $data;
    }

    protected function viewData(): array
    {
        return [
            'fullPathValues' => $this->getFullPathValues(),
            'isRemovable' => $this->isRemovable(),
            'removableAttributes' => $this->getRemovableAttributes(),
            'canDownload' => $this->canDownload(),
            'names' => $this->resolveNames(),
            'itemAttributes' => $this->resolveItemAttributes(),
        ];
    }
}
