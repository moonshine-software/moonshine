<?php

declare(strict_types=1);

namespace MoonShine\QueryTags;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Support\Condition;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;

/**
 * @method static static make(Closure|string $label, Closure $builder)
 */
final class QueryTag
{
    use Makeable;
    use WithIcon;
    use HasCanSee;
    use WithLabel;

    protected bool $isDefault = false;

    public function __construct(
        Closure|string $label,
        protected Closure $builder,
    ) {
        $this->setLabel($label);
    }

    public function uri(): string
    {
        return str($this->label())->slug()->value();
    }

    public function default(Closure|bool|null $condition = null): self
    {
        $this->isDefault = Condition::boolean($condition, true);

        return $this;
    }

    public function isActive(): bool
    {
        return ($this->isDefault && ! request()->filled('query-tag')) || request('query-tag') === $this->uri();
    }

    public function apply(Builder $builder): Builder
    {
        return value($this->builder, $builder);
    }
}
