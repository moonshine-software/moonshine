<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts\Resource;

/**
 * @template TBuilder of mixed = mixed
 */
interface WithQueryBuilderContract
{
    public function hasWith(): bool;

    /**
     * @return TBuilder
     */
    public function newQuery(): mixed;

    /**
     * @return TBuilder
     */
    public function getQuery(): mixed;

    /**
     * @param TBuilder $builder
     */
    public function customQueryBuilder(mixed $builder): static;

    public function isDisabledQueryFeatures(): bool;

    public function disableQueryFeatures(): static;

    public function disableSaveQueryState(): static;

    public function getSortColumn(): string;

    public function getSortDirection(): string;

    public function setPaginatorPage(?int $page): static;

    public function isPaginationUsed(): bool;
}
