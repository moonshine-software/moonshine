<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\Support\Collection;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\DTOs\FileItem;

/** @method static static make(array $files = [], bool $download = true) */
final class Files extends MoonShineComponent
{
    protected string $view = 'moonshine::components.files';

    /**
     * @param list<string|FileItem|array{full_path?: null|string, raw_value?: null|string, name?: null|string, attributes?: null|array<string, mixed>}> $files
     */
    public function __construct(
        public array $files = [],
        public bool $download = true,
    ) {
        parent::__construct();

        $this->files = (new Collection($this->files))
            ->mapWithKeys(
                static fn (string|FileItem|array $value, int $index): array
                    => [
                    $index => $value instanceof FileItem
                        ? $value->toArray()
                        : (new FileItem(
                            $value['full_path'] ?? $value,
                            $value['raw_value'] ?? $value,
                            $value['name'] ?? $value,
                            $value['attributes'] ?? new MoonShineComponentAttributeBag(),
                        ))->toArray(),
                ],
            )->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'files' => $this->files,
        ];
    }
}
