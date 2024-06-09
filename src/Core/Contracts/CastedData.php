<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts;

/**
 * @template-covariant T
 */
interface CastedData
{
    /**
     * @return T
     */
    public function getOriginal(): mixed;

    public function getKey(): int|string|null;

    public function toArray(): array;
}
