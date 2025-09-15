<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Core\Exceptions\PageException;
use MoonShine\Crud\Contracts\Fields\HasAsyncSearchContract;
use MoonShine\Crud\Contracts\Fields\HasRelatedValuesContact;
use MoonShine\Laravel\Traits\Fields\BelongsToOrManyCreatable;
use MoonShine\Laravel\Traits\Fields\WithAsyncSearch;
use MoonShine\Laravel\Traits\Fields\WithRelatedValues;
use MoonShine\Support\Enums\Action;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeObject;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Traits\Fields\ConfigurableSelect;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\Searchable;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Fields\WithEscapedValue;
use Throwable;

/**
 * @template-covariant R of \Illuminate\Database\Eloquent\Relations\BelongsTo
 *
 * @extends ModelRelationField<R>
 */
class BelongsTo extends ModelRelationField implements
    HasAsyncSearchContract,
    HasRelatedValuesContact,
    HasDefaultValueContract,
    CanBeObject
{
    use WithRelatedValues;
    use WithAsyncSearch;
    use Searchable;
    use WithDefaultValue;
    use HasPlaceholder;
    use BelongsToOrManyCreatable;
    use WithEscapedValue;
    use ConfigurableSelect;

    protected string $view = 'moonshine::fields.relationships.belongs-to';

    protected bool $toOne = true;

    /**
     * @throws Throwable
     */
    protected function resolvePreview(): string
    {
        if (! $this->getResource()->hasAnyAction(Action::VIEW, Action::UPDATE)) {
            return $this->isUnescape()
                ? parent::resolvePreview()
                : $this->escapeValue((string)parent::resolvePreview());
        }

        if (! $this->hasLink() && $this->toValue()) {
            $page = $this->getResource()->hasAction(Action::UPDATE)
                ? $this->getResource()->getFormPage()
                : $this->getResource()->getDetailPage();

            if (\is_null($page)) {
                throw PageException::required();
            }

            $this->link(
                $this->getResource()->getPageUrl($page, ['resourceItem' => $this->getValue()]),
                withoutIcon: true,
            );
        }

        return $this->isUnescape()
            ? parent::resolvePreview()
            : $this->escapeValue((string)parent::resolvePreview());
    }

    protected function resolveValue(): mixed
    {
        if (\is_scalar($this->toValue())) {
            return $this->toValue();
        }

        return $this->toValue()?->getKey();
    }

    public function isSelected(string $value): bool
    {
        if (! $this->toValue()) {
            return false;
        }

        return (string)$this->toValue()->getKey() === $value;
    }

    protected function resolveOnApply(): ?Closure
    {
        return function (Model $item): Model {
            $value = $this->getRequestValue();

            if ($value === false && ! $this->isNullable()) {
                return $item;
            }

            if (self::$silentApply) {
                data_set($item, $this->getColumn(), $value);

                return $item;
            }

            if ($value === false && $this->isNullable()) {
                return $item
                    ->{$this
                        ->getRelationName()}()
                    ->dissociate();
            }

            return $item->{$this
                ->getRelationName()}()
                ->associate($value);
        };
    }

    public function getReactiveValue(): mixed
    {
        $value = $this->getValue();

        if ($value === null && ! $this->isNullable()) {
            $options = $this->getValues();
            $values = $options->getValues();

            $value = $values->count() ? $values->first()->getValue() : null;
        }

        return $value;
    }

    public function prepareReactivityValue(mixed $value, mixed &$casted, array &$except): ?Model
    {
        $value = data_get($value, 'value', $value);

        $casted = $this->getRelatedModel();
        $model = $this->makeRelatedModel($value, related: $this->getRelation()?->getRelated());
        $casted?->setRelation($this->getRelationName(), $model);

        return $model;
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this->asyncSettings([
            'selectedValuesKey' => 'value',
            'withAllFields' => true,
        ]);
    }

    /**
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'values' => $this->getRelation() ? $this->getValues()->toArray() : [],
            'isNullable' => $this->isNullable(),
            'isAsyncSearch' => $this->isAsyncSearch(),
            'asyncSearchUrl' => $this->isAsyncSearch() ? $this->getAsyncSearchUrl() : '',
            'isCreatable' => $this->isCreatable(),
            'createButton' => $this->getCreateButton(),
            'fragmentUrl' => $this->getFragmentUrl(),
            'relationName' => $this->getRelationName(),
            ...$this->getSelectViewData(),
        ];
    }
}
