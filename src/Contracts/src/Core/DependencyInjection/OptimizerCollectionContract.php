<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

interface OptimizerCollectionContract
{
    public function getCachePath(): string;

    /**
     * @return array<array-key, mixed>
     */
    public function getType(string $contract, ?string $namespace = null, bool $withCache = true): array;

    /**
     * @return array<class-string, mixed>
     */
    public function getTypes(?string $namespace = null, bool $withCache = true): array;

    public function hasType(string $contract): bool;

    public function hasCache(): bool;
}
