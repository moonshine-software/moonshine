<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use MoonShine\Buttons\DeleteButton;
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

        return $this->getFields()->detailFields();
    }

    /**
     * @throws Throwable
     */
    protected function resolvePreview(): View|string
    {
        $items = Arr::wrap($this->toValue());

        if ($this->isRawMode()) {
            return collect($items)
                ->map(fn (Model $item) => $item->{$this->getResourceColumn()})
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

        $parentResource = moonshineRequest()->getResource();

        $item = $this->toValue();
        // When need lazy load
        // $item->load($resource->getWith());

        if (is_null($parentResource)) {
            throw new FieldException('Parent resource is required');
        }

        $parentItem = $parentResource->getItemOrInstance();

        $fields = $resource->getFormFields();
        $fields->onlyFields()->each(fn (Field $field): Field => $field->setParent($this));

        $action = $resource->route(
            is_null($item) ? 'crud.store' : 'crud.update',
            $item?->getKey()
        );

        $fields->exceptElements(
            fn (mixed $nestedFields): bool => $nestedFields instanceof ModelRelationField
                && $nestedFields->getResource() === moonshineRequest()->getResource()
        );

        $redirectAfter = to_page(
            page: $resource->formPage(),
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
                )
                ->toArray()
            )
            ->redirect($isAsync ? null : $redirectAfter)
            ->fillCast(
                $item?->attributesToArray() ?? [
                $this->getRelation()?->getForeignKeyName() => $this->getRelatedModel()?->getKey(),
            ],
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
            ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg']);
    }

    protected function resolveAfterDestroy(mixed $data): mixed
    {
        $this->getResource()
            ->getFormFields()
            ->each(fn(Field $field): mixed => $field->resolveFill($data->toArray())->afterDestroy($data));

        return $data;
    }
}
