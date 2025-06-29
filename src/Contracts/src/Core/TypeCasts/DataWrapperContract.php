<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\TypeCasts;

/**
 * @template-covariant T of mixed = mixed
 */
interface DataWrapperContract
{
    /**
     * @return T
     */
    public function getOriginal(): mixed;

    public function getKey(): int|string|null;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
