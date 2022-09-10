<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Closure;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Contracts\FilterContract;

class Filter implements FilterContract
{
    use Makeable;

    public function __construct(
        protected string $label,
        protected array $fields,
        protected Closure $query
    ) {
    }

    public function label(): string
    {
        return $this->label;
    }

    public function queryCallback(...$args)
    {
        return call_user_func($this->query, ...$args);
    }

    public function fields(): Fields
    {
        return Fields::make($this->fields);
    }

    public function jsonSerialize(): array
    {
        return [
            'label' => $this->label(),
            'fields' => $this->fields(),
            'values' => $this->fields()->requestValues('filters')
        ];
    }
}
