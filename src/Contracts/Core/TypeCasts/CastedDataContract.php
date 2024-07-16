<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\TypeCasts;

/**
 * @template-covariant T
 */
interface CastedDataContract
{
    /**
     * @return T
     */
    public function getOriginal(): mixed;

    public function getKey(): int|string|null;

    public function toArray(): array;
}
