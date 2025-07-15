<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\PageContract;

/**
 * @template TResource of CrudResourceContract = CrudResourceContract
 * @template TPage of PageContract = PageContract
 */
interface CrudRequestContract
{
    /**
     * @return TResource|null
     */
    public function getResource(): ?CrudResourceContract;

    public function getResourceUri(): ?string;

    public function hasResource(): bool;

    /**
     * @return TPage|null
     */
    public function findPage(): ?PageContract;

    /**
     * @return TPage
     */
    public function getPage(): PageContract;

    public function getPageUri(): ?string;

    public function getItemID(): int|string|null;

    public function getComponentName(): string;

    public function getFragmentLoad(): ?string;

    public function isFragmentLoad(?string $name = null): bool;

    public function isMoonShineRequest(): bool;

    public function getParentResourceId(): ?string;

    public function getParentRelationName(): ?string;

    public function getParentRelationId(): int|string|null;
}
