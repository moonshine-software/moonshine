<?php

namespace MoonShine\Filters;

use Illuminate\Contracts\Database\Query\Builder;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Traits\Fields\NumberTrait;
use MoonShine\Traits\Fields\SlideTrait;
use MoonShine\Traits\Fields\WithDefaultValue;

class SlideFilter extends Filter implements
    HasDefaultValue,
    DefaultCanBeArray
{
    use NumberTrait;
    use SlideTrait;
    use WithDefaultValue;

    protected static string $view = 'moonshine::filters.slide';
    public string $type = 'number';
    protected array $attributes = [
        'type',
        'min',
        'max',
        'step',
        'disabled',
        'readonly',
        'required',
    ];

    protected array $values;

    protected function resolveQuery(Builder $query): Builder
    {
        $this->values = array_filter($this->requestValue(), 'is_numeric');

        return $query->where(function (Builder $query): void {
            $query
                ->where($this->field(), '>=', $this->values[$this->fromField] ?? $this->min)
                ->where($this->field(), '<=', $this->values[$this->toField] ?? $this->max)
                ->when(
                    ($this->isNullable() && !$this->isChanged()),
                    fn(Builder $query) => $query->orWhereNull($this->field())
                );
        });
    }

    public function isChanged(): bool
    {
        $fromValue = (float)($this->values[$this->fromField] ?? $this->min);
        $toValue = (float)($this->values[$this->toField] ?? $this->max);

        return $fromValue !== (float)$this->min || $toValue !== (float)$this->max;
    }
}
