<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

interface HasStructureContract
{
    /**
     * @return array<string, mixed>
     */
    public function toStructure(bool $withStates = true): array;
}
