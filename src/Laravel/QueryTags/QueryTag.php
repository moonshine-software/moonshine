<?php

declare(strict_types=1);

namespace MoonShine\Laravel\QueryTags;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Support\Traits\HasCanSee;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithIcon;
use MoonShine\Support\Traits\WithLabel;
use MoonShine\UI\Contracts\Components\HasCanSeeContract;

/**
 * @method static static make(Closure|string $label, Closure $builder)
 */
final class QueryTag implements HasCanSeeContract
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
        if(! is_null($this->alias)) {
            return $this->alias;
        }

        return str($this->getLabel())->slug()->value();
    }

    public function default(Closure|bool|null $condition = null): self
    {
        $this->isDefault = value($condition, $this) ?? true;

        return $this;
    }

    public function isActive(): bool
    {
        if ($this->isDefault && ! request()->filled('query-tag')) {
            return true;
        }

        return request('query-tag') === $this->uri();
    }

    public function apply(Builder $builder): Builder
    {
        return value($this->builder, $builder);
    }
}
