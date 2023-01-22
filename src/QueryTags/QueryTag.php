<?php

declare(strict_types=1);

namespace Leeto\MoonShine\QueryTags;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Leeto\MoonShine\Traits\Fields\HasCanSee;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithIcon;

final class QueryTag
{
    use Makeable;
    use WithIcon;
    use HasCanSee;

    public function __construct(
        protected string $label,
        protected Builder $builder,
    ) {
    }

    public function label(): string
    {
        return $this->label;
    }

    public function uri(): string
    {
        return str($this->label())->slug()->value();
    }

    public function builder(): Builder
    {
        return $this->builder;
    }
}
