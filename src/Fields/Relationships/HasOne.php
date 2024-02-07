<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Support\Arr;
use MoonShine\Buttons\DeleteButton;
use MoonShine\Collections\MoonShineRenderElements;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Traits\WithFields;
use Throwable;

/**
 * @extends ModelRelationField<\Illuminate\Database\Eloquent\Relations\HasOne>
 */
class HasOne extends ModelRelationField implements HasFields
{
    use WithFields;

    protected string $view = 'moonshine::fields.relationships.has-one';

    protected bool $toOne = true;

    protected bool $isGroup = true;

    protected bool $outsideComponent = true;

    protected bool $isAsync = false;

    public function async(): static
    {
        $this->isAsync = true;

        return $this;
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }

    /**
     * @throws Throwable
     */
    public function preparedFields(): Fields
    {
        if (! $this->hasFields()) {
            $fields = $this->getResource()->getDetailFields();

            $this->fields($fields->toArray());

            return Fields::make($this->fields);
        }

        return $this->getFields()
            ->onlyFields(withWrappers: true)
            ->detailFields(withOutside: false);
    }

    /**
     * @throws Throwable
     */
    protected function resolvePreview(): View|string
    {
        $items = Arr::wrap($this->toValue());

        if ($this->isRawMode()) {
            return collect($items)
                ->map(fn (Model $item) => data_get($item, $this->getResourceColumn()))
                ->implode(';');
        }

        $resource = $this->getResource();

        return TableBuilder::make(items: $items)
            ->fields($this->preparedFields())
            ->cast($resource->getModelCast())
            ->preview()
            ->simple()
            ->vertical()
            ->render();
    }

    /**
     * @throws FieldException
     * @throws Throwable
     */
    protected function resolveValue(): MoonShineRenderable
    {
        $resource = $this->getResource();

        /** @var \MoonShine\Resources\ModelResource $parentResource */
        $parentResource = moonshineRequest()->getResource();

        $item = $this->toValue();
        // When need lazy load
        // $item->load($resource->getWith());

        if (is_null($parentResource)) {
            throw new FieldException('Parent resource is required');
        }

        $parentItem = $parentResource->getItemOrInstance();
        $relation = $parentItem->{$this->getRelationName()}();

        $fields = $resource->getFormFields();
        $fields->onlyFields()->each(fn (Field $field): Field => $field->setParent($this));

        $action = $resource->route(
            is_null($item) ? 'crud.store' : 'crud.update',
            $item?->getKey()
        );

        $redirectAfter = to_page(
            page: $parentResource->formPage(),
            resource: $parentResource,
            params: ['resourceItem' => $parentItem->getKey()]
        );

        $isAsync = ! is_null($item) && ($this->isAsync() || $resource->isAsync());

        return FormBuilder::make($action)
            ->switchFormMode($isAsync)
            ->name($this->getRelationName())
            ->fields(
                $fields->when(
                    ! is_null($item),
                    fn (Fields $fields): Fields => $fields->push(
                        Hidden::make('_method')->setValue('PUT'),
                    )
                )->push(
                    Hidden::make($this->getRelation()?->getForeignKeyName())
                        ->setValue($this->getRelatedModel()?->getKey())
                )->when(
                    $this->getRelation() instanceof MorphOneOrMany,
                    fn (Fields $f) => $f->push(
                        Hidden::make($this->getRelation()?->getMorphType())
                            ->setValue($this->getRelatedModel()::class)
                    )
                )
                ->toArray()
            )
            ->redirect($isAsync ? null : $redirectAfter)
            ->fillCast(
                $item?->attributesToArray() ?? array_filter([
                $this->getRelation()?->getForeignKeyName() => $this->getRelatedModel()?->getKey(),
                ...$this->getRelation() instanceof MorphOneOrMany
                    ? [$this->getRelation()?->getMorphType() => $this->getRelatedModel()::class]
                    : [],
            ], static fn ($value) => filled($value)),
                $resource->getModelCast()
            )
            ->buttons(
                is_null($item)
                    ? []
                    : [
                    DeleteButton::for(
                        $resource,
                        redirectAfterDelete: $redirectAfter
                    )->customAttributes(['class' => 'btn-lg']),
                ]
            )
            ->onBeforeFieldsRender(fn (Fields $fields): MoonShineRenderElements => $fields->exceptElements(
                fn (mixed $field): bool => $field instanceof ModelRelationField
                    && $field->toOne()
                    && $field->column() === $relation->getForeignKeyName()
            ))
            ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg']);
    }

    /**
     * @throws FieldException
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'form' => $this->resolveValue(),
        ];
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterDestroy(mixed $data): mixed
    {
        $this->getResource()
            ->getFormFields()
            ->onlyFields()
            ->each(fn (Field $field): mixed => $field->resolveFill($data->toArray(), $data)->afterDestroy($data));

        return $data;
    }
}
