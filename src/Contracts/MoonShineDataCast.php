<?php

declare(strict_types=1);

namespace MoonShine\Contracts;

interface MoonShineDataCast
{
    public function getClass(): string;

    public function hydrate(array $data): mixed;

    public function dehydrate(mixed $data): array;
}
