<?php

declare(strict_types=1);

namespace MoonShine\QueryTags;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;

/**
 * @method static static make(string $label, Closure $builder)
 */
final class QueryTag
{
    use Makeable;
    use WithIcon;
    use HasCanSee;
    use WithLabel;

    public function __construct(
        string $label,
        protected Closure $builder,
    ) {
        $this->setLabel($label);
    }

    public function uri(): string
    {
        return str($this->label())->slug()->value();
    }

    public function builder(Builder $builder): Builder
    {
        return is_callable($this->builder)
            ? call_user_func($this->builder, $builder)
            : $this->builder;
    }
}
