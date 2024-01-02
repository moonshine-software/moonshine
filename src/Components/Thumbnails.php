<?php

declare(strict_types=1);

namespace MoonShine\Components;

/** @method static static make(string|array|null $valueOrValues = null) */
final class Thumbnails extends MoonShineComponent
{
    protected string $view = 'moonshine::components.thumbnails';

    public function __construct(
        protected string|array|null $valueOrValues = null,
    ) {
    }

    protected function viewData(): array
    {
        if(is_null($this->valueOrValues)) {
            return [];
        }

        if(is_string($this->valueOrValues)) {
            return [
                'value' => $this->valueOrValues,
            ];
        }

        return [
            'values' => $this->valueOrValues,
        ];
    }
}
