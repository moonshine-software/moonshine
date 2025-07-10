<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\Support\Collection;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\DTOs\FileItem;

/** @method static static make(FileItem|string|array|null $items) */
final class Thumbnails extends MoonShineComponent
{
    protected string $view = 'moonshine::components.thumbnails';

    /**
     * @param list<string|FileItem|array{full_path?: null|string, raw_value?: null|string, name?: null|string, attributes?: null|MoonShineComponentAttributeBag|array<string, mixed>}> $items
     */
    public function __construct(
        protected FileItem|string|array|null $items,
    ) {
        parent::__construct();

        if (\is_array($this->items)) {
            $this->items = (new Collection($this->items))
                ->mapWithKeys(
                    static fn (string|array|FileItem $value, int $index): array => [
                        $index => $value instanceof FileItem
                            ? $value->toArray()
                            : (new FileItem(
                                $value['full_path'] ?? $value,
                                $value['raw_value'] ?? $value['full_path'] ?? $value,
                                $value['name'] ?? '',
                                isset($value['attributes']) && $value['attributes'] instanceof MoonShineComponentAttributeBag
                                    ? $value['attributes']
                                    : new MoonShineComponentAttributeBag($value['attributes'] ?? []),
                            ))->toArray(),
                    ]
                )->toArray();
        }

    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        if (\is_null($this->items)) {
            return [
                'values' => [],
            ];
        }

        if (\is_string($this->items)) {
            $this->items = new FileItem(
                $this->items,
                $this->items,
                $this->items
            );
        }

        if ($this->items instanceof FileItem) {
            return [
                'value' => $this->items->toArray(),
            ];
        }

        return [
            'values' => $this->items,
        ];
    }
}
