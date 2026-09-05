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
use MoonShine\Support\Stringify;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeObject;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Traits\Fields\ConfigurableSelect;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\Searchable;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Fields\WithEscapedValue;
use Throwable;

/**
 * @template-covariant R of \Illuminate\Database\Eloquent\Relations\BelongsTo<Model, Model> = \Illuminate\Database\Eloquent\Relations\BelongsTo<Model, Model>
 *
 * @implements HasAsyncSearchContract<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Relations\Relation<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Model, mixed>, \MoonShine\Laravel\Http\Requests\Relations\RelationModelFieldRequest>
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
        if (! $this->getResourceOrFail()->hasAnyAction(Action::VIEW, Action::UPDATE)) {
            return $this->isUnescape()
                ? $this->stringifySlotContent(parent::resolvePreview())
                : $this->escapeValue($this->stringifySlotContent(parent::resolvePreview()));
        }

        if (! $this->hasLink() && $this->toValue()) {
            $page = $this->getResourceOrFail()->hasAction(Action::UPDATE)
                ? $this->getResourceOrFail()->getFormPage()
                : $this->getResourceOrFail()->getDetailPage();

            if (\is_null($page)) {
                throw PageException::required();
            }

            $this->link(
                $this->getResourceOrFail()->getPageUrl($page, ['resourceItem' => Stringify::value($this->getValue())]),
                withoutIcon: true,
            );
        }

        return $this->isUnescape()
            ? $this->stringifySlotContent(parent::resolvePreview())
            : $this->escapeValue($this->stringifySlotContent(parent::resolvePreview()));
    }

    protected function resolveValue(): mixed
    {
        $value = $this->toValue();
        if (\is_scalar($value)) {
            return $value;
        }

        return $value instanceof Model ? $value->getKey() : null;
    }

    public function isSelected(string $value): bool
    {
        if (! $this->toValue()) {
            return false;
        }

        return Stringify::value($this->resolveValue()) === $value;
    }

    protected function resolveOnApply(): ?Closure
    {
        return function (Model $item): Model {
            /** @var Model|int|string|false|null $value */
            $value = $this->getRequestValue();

            if ($value === false && ! $this->isNullable()) {
                return $item;
            }

            if (self::$silentApply) {
                $item->setAttribute($this->getColumn(), $value);

                return $item;
            }

            if ($value === false) {
                return $this->getRelationFor($item)->dissociate();
            }

            return $this->getRelationFor($item)->associate($value);
        };
    }

    public function getReactiveValue(): mixed
    {
        $value = $this->getValue();

        if ($value === null && ! $this->isNullable()) {
            $options = $this->getValues();
            $values = $options->getValues();

            $option = $values->first();
            $value = $option instanceof \MoonShine\Support\DTOs\Select\Option ? $option->getValue() : null;
        }

        return $value;
    }

    public function prepareReactivityValue(mixed $value, mixed &$casted, array &$except): ?Model
    {
        /** @var int|string|null $value */
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
            'values' => $this->getRelation() instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo ? $this->getValues()->toArray() : [],
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
