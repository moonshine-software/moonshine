<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Fields;

interface HasValueExtraction
{
    public function extractValues(array $data): array;
}
