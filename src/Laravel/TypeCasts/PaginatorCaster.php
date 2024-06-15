<?php

declare(strict_types=1);

namespace MoonShine\Laravel\TypeCasts;

use MoonShine\Core\Paginator\Paginator;
use MoonShine\Core\Paginator\PaginatorCasterContract;
use MoonShine\Core\Paginator\PaginatorContract;

final readonly class PaginatorCaster implements PaginatorCasterContract
{
    public function __construct(private array $data)
    {
    }

    public function cast(): PaginatorContract
    {
        $data = collect($this->data)
            ->mapWithKeys(
                fn(mixed $value, string $key) => [(string) str($key)->camel() => $value]
            )
            ->toArray();

        if(!isset($data['links'])) {
            $data['links'] = [];
            $data['simple'] = true;
        }

        return new Paginator(...$data);
    }
}
