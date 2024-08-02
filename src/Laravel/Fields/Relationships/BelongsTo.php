<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\UI\HasReactivityContract;
use MoonShine\Core\Exceptions\PageException;
use MoonShine\Core\Traits\HasResource;
use MoonShine\Laravel\Contracts\Fields\HasAsyncSearchContract;
use MoonShine\Laravel\Contracts\Fields\HasRelatedValuesContact;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Traits\Fields\BelongsToOrManyCreatable;
use MoonShine\Laravel\Traits\Fields\WithAsyncSearch;
use MoonShine\Laravel\Traits\Fields\WithRelatedValues;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeObject;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\Reactivity;
use MoonShine\UI\Traits\Fields\Searchable;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use Throwable;

/**
 * @extends ModelRelationField<\Illuminate\Database\Eloquent\Relations\BelongsTo>
 * @extends HasResource<ModelResource, ModelResource>
 */
class BelongsTo extends ModelRelationField implements
    HasAsyncSearchContract,
    HasRelatedValuesContact,
    HasDefaultValueContract,
    CanBeObject,
    HasReactivityContract
{
    use WithRelatedValues;
    use WithAsyncSearch;
    use Searchable;
    use WithDefaultValue;
    use HasPlaceholder;
    use Reactivity;
    use BelongsToOrManyCreatable;

    protected string $view = 'moonshine::fields.relationships.belongs-to';

    protected bool $toOne = true;

    /**
     * @throws Throwable
     */
    protected function resolvePreview(): string
    {
        if (! $this->getResource()->hasAction(Action::VIEW, Action::UPDATE)) {
            return parent::resolvePreview();
        }

        if (! $this->hasLink() && $this->toValue()) {
            $page = $this->getResource()->hasAction(Action::UPDATE)
                ? $this->getResource()->getFormPage()
                : $this->getResource()->getDetailPage();

            throw_if(is_null($page), PageException::required());

            $this->link(
                $this->getResource()->getPageUrl($page, ['resourceItem' => $this->getData()?->getKey()]),
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
            $value = $this->getRequestValue();

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
            'asyncSearchUrl' => $this->getAsyncSearchUrl(),
            'isCreatable' => $this->isCreatable(),
            'createButton' => $this->getCreateButton(),
            'fragmentUrl' => $this->getFragmentUrl(),
            'relationName' => $this->getRelationName(),
        ];
    }
}
