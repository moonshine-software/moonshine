<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\RenderableContract;
use MoonShine\Contracts\Core\TypeCasts\CastedDataContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Core\Traits\HasResource;
use MoonShine\Laravel\Buttons\HasManyButton;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Traits\Fields\WithRelatedLink;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Contracts\HasUpdateOnPreviewContract;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Traits\WithFields;
use Throwable;

/**
 * @extends ModelRelationField<\Illuminate\Database\Eloquent\Relations\HasMany>
 * @extends HasResource<ModelResource, ModelResource>
 */
class HasMany extends ModelRelationField implements HasFieldsContract
{
    /** @use WithFields<Fields> */
    use WithFields;
    use WithRelatedLink;

    protected string $view = 'moonshine::fields.relationships.has-many';

    protected bool $isGroup = true;

    protected bool $hasOld = false;

    protected bool $resolveValueOnce = true;

    protected bool $outsideComponent = true;

    protected int $limit = 15;

    protected bool $isCreatable = false;

    protected bool $isSearchable = true;

    protected bool $isAsync = true;

    protected ?ActionButtonContract $createButton = null;

    protected ?ActionButtonContract $editButton = null;

    protected ?Closure $modifyTable = null;

    protected ?Closure $modifyCreateButton = null;

    protected ?Closure $modifyEditButton = null;

    protected ?Closure $modifyItemButtons = null;

    protected ?Closure $modifyBuilder = null;

    protected ?Closure $redirectAfter = null;

    protected bool $withoutModals = false;

    public function withoutModals(): static
    {
        $this->withoutModals = true;

        return $this;
    }

    public function isWithoutModals(): bool
    {
        return $this->withoutModals;
    }

    /**
     * @param  Closure(int $parentId, static $field): string  $callback
     */
    public function redirectAfter(Closure $callback): static
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
            ?->getFormPageUrl($parentId) ?? '';
    }

    /**
     * @param  Closure(ActionButtonContract $button, static $ctx): ActionButtonContract  $callback
     */
    public function modifyCreateButton(Closure $callback): static
    {
        $this->modifyCreateButton = $callback;

        return $this;
    }

    /**
     * @param  Closure(ActionButtonContract $button, static $ctx): ActionButtonContract  $callback
     */
    public function modifyEditButton(Closure $callback): static
    {
        $this->modifyEditButton = $callback;

        return $this;
    }

    /**
     * @param  Closure(ActionButtonContract $detail, ActionButtonContract $edit, ActionButtonContract $delete, ActionButtonContract $massDelete, static $ctx): array  $callback
     */
    public function modifyItemButtons(Closure $callback): static
    {
        $this->modifyItemButtons = $callback;

        return $this;
    }

    /**
     * @param  Closure(TableBuilderContract $table, bool $preview, static $ctx): TableBuilderContract  $callback
     */
    public function modifyTable(Closure $callback): static
    {
        $this->modifyTable = $callback;

        return $this;
    }

    /**
     * @param  Closure(Relation $relation, self $field): Relation  $builder
     */
    public function modifyBuilder(Closure $builder): static
    {
        $this->modifyBuilder = $builder;

        return $this;
    }

    public function hasWrapper(): bool
    {
        return false;
    }

    public function creatable(
        Closure|bool|null $condition = null,
        ?ActionButtonContract $button = null,
    ): static {
        $this->isCreatable = value($condition, $this) ?? true;
        $this->createButton = $button;

        return $this;
    }

    public function changeEditButton(?ActionButtonContract $button = null): static
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
        $this->isSearchable = value($condition, $this) ?? true;

        return $this;
    }

    public function isSearchable(): bool
    {
        return $this->isSearchable;
    }

    /**
     * @throws Throwable
     */
    public function getCreateButton(): ?ActionButtonContract
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

    public function disableAsync(): static
    {
        $this->isAsync = false;

        return $this;
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }

    /**
     * @throws Throwable
     */
    public function getPreparedFields(): FieldsContract
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
    public function preparedClonedFields(): FieldsContract
    {
        $fields = $this->getPreparedFields();

        return $this->hasFields()
            ? $fields->map(static fn (FieldContract $field): FieldContract => (clone $field))
            //If there are no fields, then the resource fields always return new objects
            : $fields;
    }

    /**
     * @throws Throwable
     */
    protected function getTablePreview(): TableBuilderContract
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
                fn (TableBuilderContract $tableBuilder) => value($this->modifyTable, $tableBuilder, preview: true)
            );
    }

    /**
     * HasOne/HasMany mapper with updateOnPreview
     */
    private function getFieldsOnPreview(): Closure
    {
        return function () {
            $fields = $this->preparedClonedFields();

            // the onlyFields method is needed to exclude stack fields
            $fields->onlyFields()->each(function (FieldContract $field): void {
                if ($field instanceof HasUpdateOnPreviewContract && $field->isUpdateOnPreview()) {
                    $field->nowOnParams(params: ['relation' => $this->getRelationName()]);
                }

                $field->setParent($this);
            });

            return $fields->toArray();
        };
    }

    /**
     * @throws Throwable
     */
    protected function getTableValue(): RenderableContract
    {
        $items = $this->getValue();
        $resource = $this->getResource();

        // Need for assets
        $resource->getFormFields();

        $asyncUrl = moonshineRouter()->getEndpoints()->toRelation(
            'search-relations',
            resourceItem: $this->getRelatedModel()?->getKey(),
            relation: $this->getRelationName()
        );

        return TableBuilder::make(items: $items)
            ->async($asyncUrl)
            ->when(
                $this->isSearchable() && ! empty($this->getResource()->search()),
                static fn (TableBuilderContract $table): TableBuilderContract => $table->searchable()
            )
            ->name($this->getRelationName())
            ->fields($this->getFieldsOnPreview())
            ->cast($resource->getModelCast())
            ->withNotFound()
            ->when(
                ! is_null($resource->trAttributes()),
                static fn (TableBuilderContract $table): TableBuilderContract => $table->trAttributes(
                    $resource->trAttributes()
                )
            )
            ->when(
                ! is_null($resource->tdAttributes()),
                static fn (TableBuilderContract $table): TableBuilderContract => $table->tdAttributes(
                    $resource->tdAttributes()
                )
            )
            ->buttons($this->getItemButtons())->when(
                ! is_null($this->modifyTable),
                fn (TableBuilderContract $tableBuilder) => value($this->modifyTable, $tableBuilder, preview: false)
            );
    }

    /**
     * @throws Throwable
     */
    protected function getItemButtons(): array
    {
        $resource = $this->getResource();

        $redirectAfter = $this->isAsync()
            ? '' : $this->getRedirectAfter(
                $this->getRelatedModel()?->getKey()
            );

        $editButton = $this->editButton ?? HasManyButton::for($this, update: true);

        if (! is_null($this->modifyEditButton)) {
            $editButton = value($this->modifyEditButton, $editButton, $this);
        }

        $detailButton = $resource->getDetailButton();

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

    protected function prepareFill(array $raw = [], ?CastedDataContract $casted = null): mixed
    {
        return null;
    }

    /**
     * @throws Throwable
     */
    protected function resolvePreview(): Renderable|string
    {
        if($this->isRawMode()) {
            return '';
        }

        // resolve value before call toValue
        if(is_null($this->toValue())) {
            $casted = $this->getRelatedModel();
            $this->setValue($casted?->{$this->getRelationName()});
        }

        return $this->isRelatedLink()
            ? $this->getRelatedLink()->render()
            : $this->getTablePreview()->render();
    }

    /**
     * @throws Throwable
     */
    protected function resolveValue(): mixed
    {
        $resource = $this->getResource();

        $resource->setQueryParams(
            request()->only($resource->getQueryParamsKeys())
        );

        $casted = $this->getRelatedModel();
        $relation = $casted?->{$this->getRelationName()}();

        $resource->customBuilder(
            is_null($this->modifyBuilder)
                ? $relation
                : value($this->modifyBuilder, $relation)
        );

        $items = $resource->paginate();

        $this->setValue($items);

        return $items;
    }

    /**
     * @throws Throwable
     */
    public function getComponent(): RenderableContract
    {
        // resolve value before call toValue
        if(is_null($this->toValue())) {
            $this->setValue($this->getValue());
        }

        return $this->isRelatedLink()
            ? $this->getRelatedLink()
            : $this->getTableValue();
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
            ->each(static fn (Field $field): mixed => $field->fillData($data)->afterDestroy($data));

        return $data;
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'component' => $this->getComponent(),
            'isCreatable' => $this->isCreatable(),
            'createButton' => $this->getCreateButton(),
        ];
    }
}
