<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

interface HasStructureContract
{
    public function toStructure(bool $withStates = true): array;
}
