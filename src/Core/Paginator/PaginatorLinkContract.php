<?php

declare(strict_types=1);

namespace MoonShine\Core\Paginator;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

interface PaginatorLinkContract extends Arrayable, JsonSerializable
{
    public function getUrl(): string;

    public function getLabel(): string;

    public function isActive(): bool;
}
