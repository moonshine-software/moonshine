<?php

declare(strict_types=1);

namespace MoonShine\Laravel\TypeCasts;

use MoonShine\Core\Paginator\Paginator;
use MoonShine\Core\Paginator\PaginatorCasterContract;
use MoonShine\Core\Paginator\PaginatorContract;

final readonly class PaginatorCaster implements PaginatorCasterContract
{
    public function __construct(
        private array $data,
        private iterable $originalData,
    ) {
    }

    public function cast(): PaginatorContract
    {
        $data = collect($this->data)
            ->mapWithKeys(
                static fn (mixed $value, string $key): array => [(string) str($key)->camel() => $value]
            )
            ->toArray();

        $data['originalData'] = $this->originalData;

        if (! isset($data['links'])) {
            $data['links'] = [];
            $data['simple'] = true;
        }

        $data['translates'] = [
            'previous' => 'moonshine::pagination.previous',
            'next' => 'moonshine::pagination.next',
            'showing' => 'moonshine::pagination.showing',
            'to' => 'moonshine::pagination.to',
            'of' => 'moonshine::pagination.of',
            'results' => 'moonshine::pagination.results',
        ];

        return new Paginator(...$data);
    }
}
