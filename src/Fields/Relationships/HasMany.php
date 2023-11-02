<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Buttons\HasOneOrManyFields\HasManyCreateButton;
use MoonShine\Buttons\HasOneOrManyFields\HasManyEditButton;
use MoonShine\Buttons\IndexPage\DeleteButton;
use MoonShine\Buttons\IndexPage\DetailButton;
use MoonShine\Buttons\IndexPage\MassDeleteButton;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasUpdateOnPreview;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Support\Condition;
use MoonShine\Traits\WithFields;
use Throwable;

class HasMany extends ModelRelationField implements HasFields
{
    use WithFields;

    protected string $view = 'moonshine::fields.relationships.has-many';

    protected bool $isGroup = true;

    protected bool $outsideComponent = true;

    protected int $limit = 15;

    protected Closure|bool $onlyLink = false;

    protected ?string $linkRelation = null;

    protected bool $isCreatable = false;

    protected bool $isAsync = false;

    public function creatable(Closure|bool|null $condition = null): static
    {
        $this->isCreatable = Condition::boolean($condition, true);

        return $this;
    }

    public function isCreatable(): bool
    {
        return $this->isCreatable;
    }

    public function createButton(): ?ActionButton
    {
        if (is_null($this->getRelatedModel()?->getKey())) {
            return null;
        }

        if (! $this->isCreatable()) {
            return null;
        }

        $button = HasManyCreateButton::for($this);

        return $button->isSee($this->getRelatedModel())
            ? $button
            : null;
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

    public function async(): static
    {
        $this->isAsync = true;

        return $this;
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }

    public function onlyLink(?string $linkRelation = null, Closure|bool|null $condition = null): static
    {
        $this->linkRelation = $linkRelation;

        if (is_null($condition)) {
            $this->onlyLink = true;

            return $this;
        }

        $this->onlyLink = $condition;

        return $this;
    }

    public function isOnlyLink(): bool
    {
        if (is_callable($this->onlyLink) && is_null($this->toValue())) {
            return value($this->onlyLink, 0, $this);
        }

        if (is_callable($this->onlyLink)) {
            $count = $this->toValue() instanceof Collection
                ? $this->toValue()->count()
                : $this->toValue()->total();

            return value($this->onlyLink, $count, $this);
        }

        return $this->onlyLink;
    }

    /**
     * @throws Throwable
     */
    public function preparedFields(): Fields
    {
        if (! $this->hasFields()) {
            $fields = $this->getResource()->getIndexFields();

            $this->fields($fields->toArray());

            return Fields::make($this->fields);
        }

        return $this->getFields()->indexFields();
    }

    protected function linkPreview(): View|string
    {
        $casted = $this->getRelatedModel();

        $countItems = $this->toValue()->count();

        if (is_null($relationName = $this->linkRelation)) {
            $relationName = str_replace('-resource', '', moonshineRequest()->getResourceUri());
        }

        return ActionButton::make(
            "($countItems)",
            to_page(
                page: IndexPage::class,
                resource: $this->getResource(),
                params: [
                    '_parentId' => $relationName . '-' . $casted->{$casted->getKeyName()},
                ]
            )
        )
            ->icon('heroicons.outline.eye')
            ->render();
    }

    /**
     * @throws Throwable
     */
    protected function tablePreview(): View|string
    {
        $items = $this->toValue();

        if (filled($items)) {
            $items = $items->take($this->getLimit());
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
            ->render();
    }

    protected function linkValue(): MoonShineRenderable
    {
        if (is_null($relationName = $this->linkRelation)) {
            $relationName = str_replace('-resource', '', moonshineRequest()->getResourceUri());
        }

        return
            ActionButton::make(
                __('moonshine::ui.show') . " ({$this->toValue()->total()})",
                to_page(
                    page: IndexPage::class,
                    resource: $this->getResource(),
                    params: [
                        '_parentId' => $relationName . '-' . $this->getRelatedModel()?->getKey(),
                    ]
                )
            )->primary();
    }

    protected function tableValue(): MoonShineRenderable
    {
        $resource = $this->getResource();

        $asyncUrl = to_relation_route(
            'search-relations',
            resourceItem: $this->getRelatedModel()?->getKey(),
            relation: $this->getRelationName()
        );

        $fields = $this->preparedFields();

        $fields->each(function (Field $field): void {
            if (
                $field instanceof HasUpdateOnPreview
                && $field->isUpdateOnPreview()
                && is_null($field->getUrl())
            ) {
                $field->setUpdateOnPreviewUrl(
                    updateRelationColumnRoute(
                        $field->getResourceUriForUpdate(),
                        $field->getPageUriForUpdate(),
                        $this->getRelationName(),
                    )
                );
            }
        });

        $fields->onlyFields()->each(fn (Field $field): Field => $field->setParent($this));

        $parentId = $this->getRelatedModel()?->getKey();

        $redirectAfter = $this->isAsync() ? '' : to_page(
            page: $resource->formPage(),
            resource: moonshineRequest()->getResource(),
            params: ['resourceItem' => $parentId]
        );

        return TableBuilder::make(items: $this->toValue())
            ->async($asyncUrl)
            ->searchable()
            ->name($this->getRelationName())
            ->fields($fields)
            ->cast($resource->getModelCast())
            ->when(
                $this->isNowOnForm(),
                fn (TableBuilder $table): TableBuilder => $table->withNotFound()
            )
            ->buttons([
                DetailButton::for($resource, $this->isAsync()),
                HasManyEditButton::for($this),
                DeleteButton::for(
                    $resource,
                    $this->getRelationName(),
                    redirectAfterDelete: $redirectAfter,
                    isAsync: $this->isAsync()
                ),
                MassDeleteButton::for(
                    $resource,
                    $this->getRelationName(),
                    redirectAfterDelete: $redirectAfter,
                    isAsync: $this->isAsync()
                ),
            ]);
    }

    protected function prepareFill(array $raw = [], mixed $casted = null): mixed
    {
        return null;
    }

    protected function resolvePreview(): View|string
    {
        if (is_null($this->toValue())) {
            $casted = $this->getRelatedModel();

            $this->setValue($casted->{$this->getRelationName()});
        }

        return $this->isOnlyLink() ? $this->linkPreview() : $this->tablePreview();
    }

    protected function resolveValue(): MoonShineRenderable
    {
        if (is_null($this->toValue())) {
            $casted = $this->getRelatedModel();

            $this->getResource()
                ->query()
                ->where(
                    $casted->{$this->getRelationName()}()->getForeignKeyName(),
                    $casted->{$casted->getKeyName()}
                );

            $this->setValue($this->getResource()->paginate());
        }

        return $this->isOnlyLink() ? $this->linkValue() : $this->tableValue();
    }

    protected function resolveOnApply(): ?Closure
    {
        return static fn ($item) => $item;
    }
}
