<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\DTOs\FileItem;

/** @method static static make(FileItem|string|list<string|FileItem|array{full_path?: null|string, raw_value?: null|string, name?: null|string, attributes?: null|MoonShineComponentAttributeBag|array<string, mixed>}>|null $items) */
final class Thumbnails extends MoonShineComponent
{
    protected string $view = 'moonshine::components.thumbnails';

    /** @var FileItem|string|list<array<string, mixed>>|null */
    protected FileItem|string|array|null $items;

    /**
     * @param FileItem|string|null|list<string|FileItem|array{full_path?: null|string, raw_value?: null|string, name?: null|string, attributes?: null|MoonShineComponentAttributeBag|array<string, mixed>}> $items
     */
    public function __construct(
        FileItem|string|array|null $items,
    ) {
        parent::__construct();

        $this->items = \is_array($items) ? array_map(
            static function (string|array|FileItem $value): array {
                if ($value instanceof FileItem) {
                    return $value->toArray();
                }

                if (\is_string($value)) {
                    return new FileItem($value, $value, '')->toArray();
                }

                return new FileItem(
                    $value['full_path'] ?? '',
                    $value['raw_value'] ?? $value['full_path'] ?? '',
                    $value['name'] ?? '',
                    isset($value['attributes']) && $value['attributes'] instanceof MoonShineComponentAttributeBag
                        ? $value['attributes']
                        : new MoonShineComponentAttributeBag($value['attributes'] ?? []),
                )->toArray();
            },
            $items,
        ) : $items;

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
