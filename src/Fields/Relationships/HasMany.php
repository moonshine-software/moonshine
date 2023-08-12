<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use MoonShine\Buttons\IndexPage\DeleteButton;
use MoonShine\Buttons\IndexPage\FormButton;
use MoonShine\Buttons\IndexPage\MassDeleteButton;
use MoonShine\Buttons\IndexPage\ShowButton;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Fields\Fields;
use MoonShine\Traits\WithFields;
use Throwable;

class HasMany extends ModelRelationField implements HasFields
{
    use WithFields;

    protected string $view = 'moonshine::fields.relationships.has-many';

    protected bool $isGroup = true;

    protected bool $outsideComponent = true;

    /**
     * @throws Throwable
     */
    protected function prepareFields(Fields $fields): Fields
    {
        if ($fields->isEmpty()) {
            $this->fields(
                $this->getResource()
                    ?->getIndexFields()
                    ?->toArray() ?? []
            );

            return Fields::make($this->fields);
        }

        return $fields;
    }

    protected function resolvePreview(): View|string
    {
        $items = $this->toValue() ?? [];

        if($this->toOne()) {
            $items = Arr::wrap($items);
        }

        if ($this->isRawMode()) {
            return $items
                ->map(fn (Model $item) => $item->{$this->getResourceColumn()})
                ->implode(';');
        }

        $resource = $this->getResource();
        $fields = $this->hasFields()
            ? $this->getFields()->indexFields()
            : $resource->getIndexFields();

        return TableBuilder::make(items: $items)
            ->fields($fields->toArray())
            ->cast($resource->getModelCast())
            ->preview()
            ->withNotFound()
            ->when(
                $this->toOne(),
                static fn (TableBuilder $table): TableBuilder => $table->vertical()
            )
            ->render();
    }

    protected function resolveValue(): mixed
    {
        $items = $this->toValue() ?? [];
        $resource = $this->getResource();
        $fields = $this->hasFields()
            ? $this->getFields()->indexFields()
            : $resource->getIndexFields();

        return TableBuilder::make(items: $items)
            ->fields($fields->toArray())
            ->cast($resource->getModelCast())
            ->withNotFound()
            ->buttons([
                ShowButton::for($resource),
                FormButton::for($resource),
                DeleteButton::for($resource),
                MassDeleteButton::for($resource),
            ]);
    }
}
