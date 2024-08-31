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

    protected ?string $alias = null;

    public function __construct(
        Closure|string $label,
        protected Closure $builder,
    ) {
        $this->setLabel($label);
    }

    public function alias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function uri(): string
    {
        if (! is_null($this->alias)) {
            return $this->alias;
        }

        return str($this->label())->slug()->value();
    }

    public function default(Closure|bool|null $condition = null): self
    {
        $this->isDefault = Condition::boolean($condition, true);

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function isActive(): bool
    {
        if ($this->isDefault() && ! request()->filled('query-tag')) {
            return true;
        }

        return request()->input('query-tag') === $this->uri();
    }

    public function apply(Builder $builder): Builder
    {
        return value($this->builder, $builder);
    }
}
