<?php

declare(strict_types=1);

namespace MoonShine\Crud\TypeCasts;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\Paginator\PaginatorCasterContract;
use MoonShine\Contracts\Core\Paginator\PaginatorContract;
use MoonShine\Core\Paginator\Paginator;

final readonly class PaginatorCaster implements PaginatorCasterContract
{
    /**
     * @param  array<array-key, mixed>  $data
     * @param  iterable<array-key, mixed>  $originalData
     */
    public function __construct(
        private array $data,
        private iterable $originalData,
        private string $pageName = 'page',
    ) {
    }

    /**
     * @return PaginatorContract<mixed>
     */
    public function cast(): PaginatorContract
    {
        $data = Collection::make($this->data)
            ->except('next_cursor', 'prev_cursor', 'current_page_url')
            ->mapWithKeys(
                static fn (mixed $value, string $key): array => [(string) Str::of($key)->camel() => $value]
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

        $data['currentPage'] ??= 1;
        $data['from'] ??= 1;
        $data['to'] ??= 1;
        $data['pageName'] ??= $this->pageName;

        return new Paginator(...$data);
    }
}
