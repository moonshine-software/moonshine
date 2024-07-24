<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use MoonShine\Support\DTOs\FileItem;
use MoonShine\UI\Components\Files;
use MoonShine\UI\Contracts\FileableContract;
use MoonShine\UI\Contracts\RemovableContract;
use MoonShine\UI\Traits\Fields\CanBeMultiple;
use MoonShine\UI\Traits\Fields\FileDeletable;
use MoonShine\UI\Traits\Fields\FileTrait;
use MoonShine\UI\Traits\Removable;

class File extends Field implements FileableContract, RemovableContract
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
        $this->getAttributes()->set('accept', $value);

        return $this;
    }

    protected function resolvePreview(): Renderable|string
    {
        $values = $this->getFullPathValues();

        if ($this->isRawMode()) {
            return implode(';', array_filter($values));
        }

        return Files::make(
            $this->getFiles()->toArray(),
            download: $this->canDownload(),
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

    protected function getFiles(): Collection
    {
        return collect($this->getFullPathValues())
            ->mapWithKeys(fn (string $path, int $index): array => [
                $index => new FileItem(
                    fullPath: $path,
                    rawValue: data_get($this->toValue(), $index, $this->toValue()),
                    name: (string) value($this->resolveNames(), $path, $index, $this),
                    attributes: value($this->resolveItemAttributes(), $path, $index, $this),
                ),
            ]);
    }

    public function getRequestValue(int|string|null $index = null): mixed
    {
        return $this->prepareRequestValue(
            $this->getCore()->getRequest()->getFile(
                $this->getRequestNameDot($index),
            ) ?? false
        );
    }

    protected function viewData(): array
    {
        return [
            'files' => $this->getFiles()->toArray(),
            'isRemovable' => $this->isRemovable(),
            'removableAttributes' => $this->getRemovableAttributes(),
            'hiddenAttributes' => $this->getHiddenAttributes(),
            'canDownload' => $this->canDownload(),
        ];
    }
}
