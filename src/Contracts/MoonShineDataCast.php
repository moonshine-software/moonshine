<?php

declare(strict_types=1);

namespace MoonShine\Contracts;

use Illuminate\Database\Eloquent\Model;

interface MoonShineDataCast
{
    public function getClass(): string|Model;

    public function hydrate(array $data): mixed;

    public function dehydrate(mixed $data): array;
}
