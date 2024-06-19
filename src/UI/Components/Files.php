<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use MoonShine\Support\DTOs\FileItem;

/** @method static static make(array $files = [], bool $download = true, ?Closure $names = null, ?Closure $itemAttributes = null) */
final class Files extends MoonShineComponent
{
    protected string $view = 'moonshine::components.files';

    public function __construct(
        public array $files = [],
        public bool $download = true,
    ) {
        parent::__construct();
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'files' => collect($this->files)
                ->mapWithKeys(
                    static fn (string|FileItem|array $value, int $index): array => [
                        $index => $value instanceof FileItem
                            ? $value->toArray()
                            : (new FileItem(
                                $value['full_path'] ?? $value,
                                $value['raw_value'] ?? $value,
                                $value['name'] ?? $value,
                                $value['attributes'] ?? $value,
                            ))->toArray(),
                    ]
                )->toArray(),
        ];
    }
}
