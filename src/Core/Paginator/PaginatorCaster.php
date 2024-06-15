<?php

declare(strict_types=1);

namespace MoonShine\Core\Paginator;

final readonly class PaginatorCaster implements PaginatorCasterContract
{
    public function __construct(private array $data)
    {
    }

    public function cast(): PaginatorContract
    {
        return new Paginator(...$this->data);
    }
}
