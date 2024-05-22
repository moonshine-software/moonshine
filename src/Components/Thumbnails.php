<?php

declare(strict_types=1);

namespace MoonShine\Components;

use MoonShine\DTOs\FileItem;

/** @method static static make(FileItem|array|null $valueOrValues) */
final class Thumbnails extends MoonShineComponent
{
    protected string $view = 'moonshine::components.thumbnails';

    public function __construct(
        protected FileItem|array|null $valueOrValues,
    ) {
        parent::__construct();
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        if(is_null($this->valueOrValues)) {
            return [
                'values' => []
            ];
        }

        if(is_string($this->valueOrValues)) {
            $this->valueOrValues = new FileItem(
                $this->valueOrValues,
                $this->valueOrValues,
                $this->valueOrValues
            );
        }

        if($this->valueOrValues instanceof FileItem) {
            return [
                'value' => $this->valueOrValues->toArray(),
            ];
        }

        return [
            'values' => collect($this->valueOrValues)
                ->mapWithKeys(
                    fn(string|array|FileItem $value, int $index) => [
                        $index => $value instanceof FileItem
                            ? $value->toArray()
                            : (new FileItem(
                                $value['full_path'] ?? $value,
                                $value['raw_value'] ?? $value,
                                $value['name'] ?? $value,
                                $value['attributes'] ?? $value,
                            ))->toArray()
                    ]
                )->toArray(),
        ];
    }
}
