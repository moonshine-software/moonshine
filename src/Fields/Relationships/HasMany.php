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

    protected bool $isGroup = true;

    protected bool $outsideComponent = true;

    /**
     * @throws Throwable
     */
    public function preparedFields(): Fields
    {
        if (! $this->hasFields()) {
            $fields = $this->toOne()
                ? $this->getResource()->getFormFields()
                : $this->getResource()->getIndexFields();

            $this->fields($fields->toArray());

            return Fields::make($this->fields);
        }

        return $this->getFields()->when(
            $this->toOne(),
            static fn (Fields $fields): Fields => $fields->formFields(),
            static fn (Fields $fields): Fields => $fields->indexFields()
        );
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

        return TableBuilder::make(items: $items)
            ->fields($this->preparedFields())
            ->cast($resource->getModelCast())
            ->preview()
            ->simple()
            ->when(
                $this->toOne(),
                static fn (TableBuilder $table): TableBuilder => $table->vertical()
            )
            ->render();
    }

    protected function resolveValue(): mixed
    {
        $resource = $this->getResource();

        $items = $resource->paginate();

        return TableBuilder::make(items: $items)
            ->async(route('moonshine.relation.search-relations', [
                'resourceItem' => request('resourceItem'),
                'pageUri' => request('pageUri'),
                'resourceUri' => request('resourceUri'),
            ]))
            ->name($this->getRelationName())
            ->fields($this->preparedFields())
            ->cast($resource->getModelCast())
            ->when(
                $this->isNowOnForm(),
                fn (TableBuilder $table): TableBuilder => $table->withNotFound()
            )
            ->buttons([
                ShowButton::for($resource),
                FormButton::for($resource),
                DeleteButton::for($resource, request()->getUri()),
                MassDeleteButton::for($resource),
            ]);
    }
}
