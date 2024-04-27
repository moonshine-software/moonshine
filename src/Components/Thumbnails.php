<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use MoonShine\Support\MoonShineComponentAttributeBag;

/** @method static static make(string|array|null $valueOrValues = null, ?Closure $names = null, ?Closure $itemAttributes = null) */
final class Thumbnails extends MoonShineComponent
{
    protected string $view = 'moonshine::components.thumbnails';

    public function __construct(
        protected string|array|null $valueOrValues = null,
        public ?Closure $names = null,
        public ?Closure $itemAttributes = null,
    ) {
        parent::__construct();
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        if(is_null($this->valueOrValues)) {
            return [];
        }

        if(is_null($this->itemAttributes)) {
            $this->itemAttributes = fn (string $filename, int $index = 0): MoonShineComponentAttributeBag => $this->attributes();
        }

        $data = [
            'names' => $this->names,
            'itemAttributes' => $this->itemAttributes,
        ];

        if(is_string($this->valueOrValues)) {
            return [
                'value' => $this->valueOrValues,
                ...$data,
            ];
        }

        return [
            'values' => $this->valueOrValues,
            ...$data,
        ];
    }
}
