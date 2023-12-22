<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeObject;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\Relationships\HasAsyncSearch;
use MoonShine\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Traits\Fields\HasPlaceholder;
use MoonShine\Traits\Fields\Searchable;
use MoonShine\Traits\Fields\WithAsyncSearch;
use MoonShine\Traits\Fields\WithDefaultValue;
use MoonShine\Traits\Fields\WithRelatedValues;

class BelongsTo extends ModelRelationField implements
    HasAsyncSearch,
    HasRelatedValues,
    HasDefaultValue,
    DefaultCanBeObject
{
    use WithRelatedValues;
    use WithAsyncSearch;
    use Searchable;
    use WithDefaultValue;
    use HasPlaceholder;

    protected string $view = 'moonshine::fields.relationships.belongs-to';

    protected bool $toOne = true;

    protected function resolvePreview(): string
    {
        $actions = $this->getResource()->getActiveActions();

        if (! in_array('view', $actions, true)
            && ! in_array('update', $actions, true)) {
            return parent::resolvePreview();
        }

        if (! $this->hasLink() && $this->toValue()) {
            $page = in_array('update', $actions, true)
                ? $this->getResource()->formPage()
                : $this->getResource()->detailPage();

            $this->link(
                $this->getResource()->pageUrl($page, ['resourceItem' => $this->toValue()->getKey()]),
                withoutIcon: true
            );
        }

        return parent::resolvePreview();
    }

    protected function resolveValue(): mixed
    {
        return $this->toValue()?->getKey();
    }

    public function isSelected(string $value): bool
    {
        if (! $this->toValue()) {
            return false;
        }

        return (string) $this->toValue()->getKey() === $value;
    }

    protected function resolveOnApply(): ?Closure
    {
        return function (Model $item) {
            $value = $this->requestValue();

            if ($value === false && ! $this->isNullable()) {
                return $item;
            }

            if ($value === false && $this->isNullable()) {
                return $item
                    ->{$this->getRelationName()}()
                    ->dissociate();
            }

            return $item->{$this->getRelationName()}()
                ->associate($value);
        };
    }
}
