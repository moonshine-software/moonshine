<?php

declare(strict_types=1);

namespace Leeto\MoonShine\QueryTags;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Leeto\MoonShine\Traits\Fields\HasCanSee;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithIcon;
use Leeto\MoonShine\Traits\WithLabel;

final class QueryTag
{
    use Makeable;
    use WithIcon;
    use HasCanSee;
    use WithLabel;

    public function __construct(
        string $label,
        protected Builder $builder,
    ) {
        $this->setLabel($label);
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
