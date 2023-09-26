<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Buttons\HasOneField\HasManyCreateButton;
use MoonShine\Buttons\IndexPage\DeleteButton;
use MoonShine\Buttons\IndexPage\DetailButton;
use MoonShine\Buttons\IndexPage\FormButton;
use MoonShine\Buttons\IndexPage\MassDeleteButton;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Decorations\Block;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\MoonShineRouter;
use MoonShine\Resources\ModelResource;
use MoonShine\Support\Condition;
use MoonShine\Traits\WithFields;
use Throwable;

class HasMany extends ModelRelationField implements HasFields
{
    use WithFields;

    protected bool $isGroup = true;

    protected bool $outsideComponent = true;

    protected int $limit = 15;

    protected bool $isOnlyLink = false;

    public function resolveFill(
        array $raw = [],
        mixed $casted = null,
        int $index = 0
    ): Field {

        if ($casted instanceof Model) {
            $this->setRelatedModel($casted);
        }

        return $this;
    }

    public function onlyLink(Closure|bool|null $condition = null): static
    {
        $this->isOnlyLink = Condition::boolean($condition, true);

        return $this;
    }


    public function isOnlyLink(): bool
    {
        return $this->isOnlyLink;
    }

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
        $casted = $this->getRelatedModel();

        $items = $casted->{$this->getRelationName()};

        if(! empty($items) && ! $this->toOne()) {
            $items = $items->take($this->getLimit());
        }

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
        if($this->isOnlyLink()) {
            return $this->buttonsValueView();
        }

        return $this->tableValueView();
    }

    protected function buttonsValueView()
    {
        $resource = $this->getResource();

        $casted = $this->getRelatedModel();

        $this->getResource()
            ->query()
            ->where(
                $casted->{$this->getRelationName()}()->getForeignKeyName(),
                $casted->{$casted->getKeyName()}
            );

        $items = $this->getResource()->query()->count();

        $parentName = str_replace('-resource', '', moonshineRequest()->getResourceUri());

        return
            ActionButton::make(
                __('moonshine::ui.show'). " ($items)",
                to_page($resource, 'index-page', ['parentId' => $parentName . '-' . request('resourceItem')])
            )
                ->customAttributes(['class' => 'btn btn-primary'])
        ;
    }

    protected function tableValueView()
    {
        $resource = $this->getResource();

        $asyncUrl = to_relation_route('search-relations', request('resourceItem'));

        $casted = $this->getRelatedModel();

        $this->getResource()
            ->query()
            ->where(
                $casted->{$this->getRelationName()}()->getForeignKeyName(),
                $casted->{$casted->getKeyName()}
            );

        $items = $this->getResource()->paginate();

        $items->setPath(to_relation_route('search-relations', request('resourceItem')));
        $fields = $this->preparedFields();
        $fields->onlyFields()->each(fn (Field $field): Field => $field->setParent($this));

        return TableBuilder::make(items: $items)
            ->async($asyncUrl)
            ->name($this->getRelationName())
            ->fields($fields)
            ->cast($resource->getModelCast())
            ->when(
                $this->isNowOnForm(),
                fn (TableBuilder $table): TableBuilder => $table->withNotFound()
            )
            ->buttons([
                DetailButton::forMode($resource),
                FormButton::forMode($resource),
                DeleteButton::for($resource, request()->getUri()),
                MassDeleteButton::for($resource),
            ]);
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    protected function resolveOnApply(): ?Closure
    {
        return static fn ($item) => $item;
    }
}
