<?php

declare(strict_types=1);

namespace MoonShine\Core\Paginator;

use MoonShine\Contracts\Core\Paginator\PaginatorCasterContract;
use MoonShine\Contracts\Core\Paginator\PaginatorContract;

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
