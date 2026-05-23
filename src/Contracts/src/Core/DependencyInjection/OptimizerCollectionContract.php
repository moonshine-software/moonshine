<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;

interface OptimizerCollectionContract
{
    public function getCachePath(): string;

    /**
     * @template T of object
     *
     * @param  class-string<T>  $contract
     *
     * @return (T is PageContract ? list<class-string<PageContract>> : (T is ResourceContract ? list<class-string<ResourceContract>> : array<array-key, mixed>))
     */
    public function getType(string $contract, ?string $namespace = null, bool $withCache = true): array;

    /**
     * @return array<class-string, mixed>
     */
    public function getTypes(?string $namespace = null, bool $withCache = true): array;

    /**
     * @param  class-string  $contract
     */
    public function hasType(string $contract): bool;

    public function hasCache(): bool;
}
