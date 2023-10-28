<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\Fields\HasFields;
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

    public function preview(): View|string
    {
        $casted = $this->getRelatedModel();

        $this->setValue($casted?->{$this->getRelationName()});

        return parent::preview();
    }

    public function value(bool $withOld = false): mixed
    {
        $this->setValue($this->getRelatedModel()->{$this->getRelationName()});

        return parent::value(false);
    }

    public function resolveFill(
        array $raw = [],
        mixed $casted = null,
        int $index = 0
    ): static {

        if ($casted instanceof Model) {
            $this->setRelatedModel($casted);
        }

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function preparedFields(): Fields
    {
        if (! $this->hasFields()) {
            $fields = $this->getResource()->getFormFields();

            $this->fields($fields->toArray());

            return Fields::make($this->fields);
        }

        return $this->getFields()->formFields();
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
    protected function resolveValue(): mixed
    {
        $resource = $this->getResource();

        $parentResource = moonshineRequest()->getResource();

        $item = $this->toValue();

        if(is_null($parentResource)) {
            throw new FieldException('Parent resource is required');
        }

        $parentItem = $parentResource->getItemOrInstance();

        $fields = $this->preparedFields();
        $fields->onlyFields()->each(fn (Field $field): Field => $field->setParent($this));

        $action = to_relation_route(
            is_null($item) ? 'store' : 'update',
            $this->getRelatedModel()?->getKey(),
        );

        $buttonUrl = fn (Model $data): string => to_relation_route(
            'delete',
            $this->getRelatedModel()?->getKey(),
            relation: $this->getRelationName()
        );

        return FormBuilder::make($action)
            ->switchFormMode($resource->isAsync() && ! is_null($item))
            ->name($this->getRelationName())
            ->fields(
                $fields->when(
                    ! is_null($item),
                    fn (Fields $fields): Fields => $fields->push(
                        Hidden::make('_method')->setValue('PUT'),
                    )
                )->push(
                    Hidden::make('_relation')->setValue($this->getRelationName()),
                )->toArray()
            )
            ->fillCast($item?->attributesToArray() ?? [
                $this->getRelation()?->getForeignKeyName() => $this->getRelatedModel()?->getKey(),
            ], $resource->getModelCast())
            ->buttons(is_null($item) ? [] : [
                ActionButton::make(
                    __('moonshine::ui.delete'),
                    url: $buttonUrl
                )
                    ->canSee(
                        fn (?Model $item): bool => ! is_null($item) && in_array('delete', $resource->getActiveActions())
                            && $resource->setItem($item)->can('delete')
                    )
                    ->secondary()
                    ->customAttributes(['class' => 'btn-lg'])
                    ->withConfirm(
                        fields: fn(Model $item) => [
                            Hidden::make($item->getKeyName())->setValue($item->getKey())
                        ],
                        method: 'DELETE',
                        formBuilder: fn(FormBuilder $form, Model $item) => $form->redirect(
                            to_page(
                                page: $resource->formPage(),
                                resource: $parentResource,
                                params: ['resourceItem' => $parentItem->getKey()]
                            )
                        )
                    )
                    ->showInLine(),
            ])
            ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg']);
    }
}
