<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Leeto\MoonShine\Traits\Makeable;

final class Filter
{
    use Makeable;

    public function __construct(
        protected string $label,
        protected array $fields
    ) {
    }

    public function fields(): array
    {
        return $this->fields;
    }
}
