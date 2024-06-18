<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Closure;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Buttons\HasManyButton;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasUpdateOnPreview;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Resources\ModelResource;
use MoonShine\Support\Condition;
use MoonShine\Traits\Fields\OnlyLink;
use MoonShine\Traits\HasResource;
use MoonShine\Traits\WithFields;
use Throwable;

/**
 * @extends ModelRelationField<\Illuminate\Database\Eloquent\Relations\HasMany>
 * @extends HasResource<ModelResource, ModelResource>
 */
class HasMany extends ModelRelationField implements HasFields
{
    use WithFields;
    use OnlyLink;

    protected string $view = 'moonshine::fields.relationships.has-many';

    protected bool $isGroup = true;

    protected bool $outsideComponent = true;

    protected int $limit = 15;

    protected bool $isCreatable = false;

    protected bool $isSearchable = true;

    protected bool $isAsync = false;

    protected ?ActionButton $createButton = null;

    protected ?ActionButton $editButton = null;

    protected ?Closure $modifyTable = null;

    protected ?Closure $modifyCreateButton = null;

    protected ?Closure $modifyEditButton = null;

    protected ?Closure $modifyItemButtons = null;

    protected ?Closure $modifyBuilder = null;

    protected ?Closure $redirectAfter = null;

    protected bool $withoutModals = false;

    public function withoutModals(): self
    {
        $this->withoutModals = true;

        return $this;
    }

    public function isWithoutModals(): bool
    {
        return $this->withoutModals;
    }

    /**
     * @param  Closure(int $parentId, self $field): string  $callback
     */
    public function redirectAfter(Closure $callback): self
    {
        $this->redirectAfter = $callback;

        return $this;
    }

    public function getRedirectAfter(Model|int|null|string $parentId): string
    {
        if(! is_null($this->redirectAfter)) {
            return value($this->redirectAfter, $parentId, $this);
        }

        return moonshineRequest()
            ->getResource()
            ?->formPageUrl($parentId) ?? '';
    }

    /**
     * @param  Closure(ActionButton $button, self $field): ActionButton  $callback
     */
    public function modifyCreateButton(Closure $callback): self
    {
        $this->modifyCreateButton = $callback;

        return $this;
    }

    /**
     * @param  Closure(ActionButton $button, self $field): ActionButton  $callback
     */
    public function modifyEditButton(Closure $callback): self
    {
        $this->modifyEditButton = $callback;

        return $this;
    }

    /**
     * @param  Closure(ActionButton $detail, ActionButton $edit, ActionButton $delete, ActionButton $massDelete, self $field): array  $callback
     */
    public function modifyItemButtons(Closure $callback): self
    {
        $this->modifyItemButtons = $callback;

        return $this;
    }

    /**
     * @param  Closure(TableBuilder $table, bool $preview, self $field): TableBuilder  $callback
     */
    public function modifyTable(Closure $callback): self
    {
        $this->modifyTable = $callback;

        return $this;
    }

    public function creatable(
        Closure|bool|null $condition = null,
        ?ActionButton $button = null,
    ): static {
        $this->isCreatable = Condition::boolean($condition, true);
        $this->createButton = $button;

        return $this;
    }

    public function changeEditButton(?ActionButton $button = null): static
    {
        $this->editButton = $button;

        return $this;
    }

    public function isCreatable(): bool
    {
        return $this->isCreatable;
    }

    public function searchable(Closure|bool|null $condition = null): static
    {
        $this->isSearchable = Condition::boolean($condition, true);

        return $this;
    }

    public function isSearchable(): bool
    {
        return $this->isSearchable;
    }

    /**
     * @throws Throwable
     */
    public function createButton(): ?ActionButton
    {
        if (is_null($this->getRelatedModel()?->getKey())) {
            return null;
        }

        if (! $this->isCreatable()) {
            return null;
        }

        $button = HasManyButton::for($this, button: $this->createButton);

        if (! is_null($this->modifyCreateButton)) {
            $button = value($this->modifyCreateButton, $button, $this);
        }

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

    /**
     * @throws Throwable
     */
    public function preparedFields(): Fields
    {
        if (! $this->hasFields()) {
            $fields = $this->getResource()->getIndexFields();

            $this->fields($fields->toArray());

            return $this->getFields();
        }

        return $this->getFields()
            ->onlyFields(withWrappers: true)
            ->indexFields();
    }

    /**
     * @throws Throwable
     */
    public function preparedClonedFields(): Fields
    {
        $fields = $this->preparedFields();

        return $this->hasFields()
            ? $fields->map(fn (Field $field): Field => (clone $field))
            //If there are no fields, then the resource fields always return new objects
            : $fields;
    }

    /**
     * @param  Closure(Relation $relation, self $field): Relation  $builder
     */
    public function modifyBuilder(Closure $builder): static
    {
        $this->modifyBuilder = $builder;

        return $this;
    }

    /**
     * @throws Throwable
     */
    protected function tablePreview(): TableBuilder
    {
        $items = $this->toValue();

        if (filled($items)) {
            $items = $items->take($this->getLimit());
        }

        if ($this->isRawMode()) {
            return $items
                ->map(fn (Model $item) => data_get($item, $this->getResourceColumn()))
                ->implode(';');
        }

        $resource = $this->getResource();

        return TableBuilder::make(items: $items)
            ->fields($this->getFieldsOnPreview())
            ->cast($resource->getModelCast())
            ->preview()
            ->simple()
            ->when(
                ! is_null($this->modifyTable),
                fn (TableBuilder $tableBuilder) => value($this->modifyTable, $tableBuilder, preview: true)
            );
    }

    /**
     * HasOne/HasMany mapper with updateOnPreview
     * TODO refactor in 3.0
     */
    private function getFieldsOnPreview(): Closure
    {
        return function () {
            $fields = $this->preparedClonedFields();

            // the onlyFields method is needed to exclude stack fields
            $fields->onlyFields()->each(function (Field $field): void {
                if (
                    $field instanceof HasUpdateOnPreview
                    && $field->isUpdateOnPreview()
                    && ! $field->hasUpdateOnPreviewCustomUrl()
                ) {
                    $field->setUpdateOnPreviewUrl(
                        moonshineRouter()->updateColumn(
                            $field->getResourceUriForUpdate(),
                            $field->getPageUriForUpdate(),
                            $this->getRelationName(),
                        )
                    );
                }

                $field->setParent($this);
            });

            return $fields->toArray();
        };
    }

    /**
     * @throws Throwable
     */
    protected function tableValue(): MoonShineRenderable
    {
        $resource = $this->getResource();

        // Need for assets
        $resource->getFormFields();

        $asyncUrl = moonshineRouter()->toRelation(
            'search-relations',
            resourceItem: $this->getRelatedModel()?->getKey(),
            relation: $this->getRelationName()
        );

        return TableBuilder::make(items: $this->toValue())
            ->async($asyncUrl)
            ->when(
                $this->isSearchable() && ! empty($this->getResource()->search()),
                fn (TableBuilder $table): TableBuilder => $table->searchable()
            )
            ->name($this->getRelationName())
            ->fields($this->getFieldsOnPreview())
            ->cast($resource->getModelCast())
            ->when(
                $this->isNowOnForm(),
                fn (TableBuilder $table): TableBuilder => $table->withNotFound()
            )
            ->when(
                ! is_null($resource->trAttributes()),
                fn (TableBuilder $table): TableBuilder => $table->trAttributes(
                    $resource->trAttributes()
                )
            )
            ->when(
                ! is_null($resource->tdAttributes()),
                fn (TableBuilder $table): TableBuilder => $table->tdAttributes(
                    $resource->tdAttributes()
                )
            )
            ->buttons($this->getItemButtons())
            ->when(
                ! is_null($this->modifyTable),
                fn (TableBuilder $tableBuilder) => value($this->modifyTable, $tableBuilder, preview: false)
            );
    }

    protected function getItemButtons(): array
    {
        $resource = $this->getResource();

        $redirectAfter = $this->isAsync()
            ? ''
            : $this->getRedirectAfter(
                $this->getRelatedModel()?->getKey()
            );

        $editButton = $this->editButton ?? HasManyButton::for($this, update: true);

        if (! is_null($this->modifyEditButton)) {
            $editButton = value($this->modifyEditButton, $editButton, $this);
        }

        $detailButton = $resource->getDetailButton(
            isAsync: $this->isAsync()
        );

        $deleteButton = $resource->getDeleteButton(
            componentName: $this->getRelationName(),
            redirectAfterDelete: $redirectAfter,
            isAsync: $this->isAsync()
        );

        $massDeleteButton = $resource->getMassDeleteButton(
            componentName: $this->getRelationName(),
            redirectAfterDelete: $redirectAfter,
            isAsync: $this->isAsync()
        );

        if(! is_null($this->modifyItemButtons)) {
            return value(
                $this->modifyItemButtons,
                $detailButton,
                $editButton,
                $deleteButton,
                $massDeleteButton,
                $this,
            );
        }

        return [
            ...$resource->getIndexButtons(),
            $detailButton,
            $editButton,
            $deleteButton,
            $massDeleteButton,
        ];
    }

    protected function prepareFill(array $raw = [], mixed $casted = null): mixed
    {
        return null;
    }

    /**
     * @throws Throwable
     */
    protected function resolvePreview(): View|string
    {
        if (is_null($this->toValue())) {
            $casted = $this->getRelatedModel();

            $this->setValue($casted?->{$this->getRelationName()});
        }

        return $this->isOnlyLink()
            ? $this->getOnlyLinkButton(preview: true)->render()
            : $this->tablePreview()->render();
    }

    /**
     * @throws Throwable
     */
    protected function resolveValue(): MoonShineRenderable
    {
        if (is_null($this->toValue())) {
            $casted = $this->getRelatedModel();
            $relation = $casted?->{$this->getRelationName()}();
            $resource = $this->getResource();

            $resource->customBuilder(
                is_null($this->modifyBuilder)
                    ? $relation
                    : value($this->modifyBuilder, $relation)
            );

            $this->setValue($resource->paginate());
        }

        return $this->isOnlyLink() ? $this->getOnlyLinkButton() : $this->tableValue();
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'table' => $this->resolveValue(),
        ];
    }

    protected function resolveOnApply(): ?Closure
    {
        return static fn ($item) => $item;
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
