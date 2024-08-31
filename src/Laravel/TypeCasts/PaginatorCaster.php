<?php

declare(strict_types=1);

namespace MoonShine\Laravel\TypeCasts;

use MoonShine\Contracts\Core\Paginator\PaginatorCasterContract;
use MoonShine\Contracts\Core\Paginator\PaginatorContract;
use MoonShine\Core\Paginator\Paginator;

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
            ->except('next_cursor', 'prev_cursor')
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

        $data['currentPage'] = $data['currentPage'] ?? 1;
        $data['from'] = $data['from'] ?? 1;
        $data['to'] = $data['to'] ?? 1;

        return new Paginator(...$data);
    }
}
