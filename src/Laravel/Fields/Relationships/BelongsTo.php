<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Core\Contracts\Fields\DefaultValueTypes\DefaultCanBeObject;
use MoonShine\Core\Contracts\Fields\HasDefaultValue;
use MoonShine\Core\Contracts\Fields\HasReactivity;
use MoonShine\Core\Contracts\Fields\Relationships\HasAsyncSearch;
use MoonShine\Core\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Core\Exceptions\PageException;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Traits\HasResource;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\Reactivity;
use MoonShine\UI\Traits\Fields\Searchable;
use MoonShine\UI\Traits\Fields\WithAsyncSearch;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Fields\WithRelatedValues;
use Throwable;

/**
 * @extends ModelRelationField<\Illuminate\Database\Eloquent\Relations\BelongsTo>
 * @extends HasResource<ModelResource, ModelResource>
 */
class BelongsTo extends ModelRelationField implements
    HasAsyncSearch,
    HasRelatedValues,
    HasDefaultValue,
    DefaultCanBeObject,
    HasReactivity
{
    use WithRelatedValues;
    use WithAsyncSearch;
    use Searchable;
    use WithDefaultValue;
    use HasPlaceholder;
    use Reactivity;

    protected string $view = 'moonshine::fields.relationships.belongs-to';

    protected bool $toOne = true;

    /**
     * @throws Throwable
     */
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

            throw_if(is_null($page), new PageException('Page is required'));

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

    /**
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'isSearchable' => $this->isSearchable(),
            'values' => $this->getRelation() ? $this->getValues()->toArray() : [],
            'isNullable' => $this->isNullable(),
            'isAsyncSearch' => $this->isAsyncSearch(),
            'asyncSearchUrl' => $this->asyncSearchUrl(),
        ];
    }
}
