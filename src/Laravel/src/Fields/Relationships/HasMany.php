<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FieldWithComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Core\Collections\Components;
use MoonShine\Crud\Contracts\Fields\HasModalModeContract;
use MoonShine\Crud\Contracts\Fields\HasOutsideSwitcherContract;
use MoonShine\Crud\Contracts\Fields\HasTabModeContract;
use MoonShine\Laravel\Buttons\HasManyButton;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Traits\Fields\HasModalModeConcern;
use MoonShine\Laravel\Traits\Fields\WithRelatedLink;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionGroup;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Contracts\HasUpdateOnPreviewContract;
use MoonShine\UI\Traits\Fields\HasTabModeConcern;
use MoonShine\UI\Traits\WithFields;
use Throwable;

/**
 * @template-covariant R of (HasOneOrMany|HasOneOrManyThrough|MorphOneOrMany)
 * @extends ModelRelationField<R>
 * @implements HasFieldsContract<Fields|FieldsContract>
 * @implements FieldWithComponentContract<TableBuilderContract|FormBuilderContract|ActionButtonContract>
 */
class HasMany extends ModelRelationField implements
    HasFieldsContract,
    FieldWithComponentContract,
    HasModalModeContract,
    HasTabModeContract,
    HasOutsideSwitcherContract
{
    use WithFields;
    use WithRelatedLink;
    use HasModalModeConcern;
    use HasTabModeConcern;

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

    protected ?ActionGroup $buttons = null;

    protected array $indexButtons = [];

    protected array $formButtons = [];

    protected ?Closure $modifyTable = null;

    protected ?Closure $modifyCreateButton = null;

    protected ?Closure $modifyEditButton = null;

    /** @var null|Closure(ActionButtonContract,ActionButtonContract,ActionButtonContract,ActionButtonContract,static): array */
    protected ?Closure $modifyItemButtons = null;

    protected ?Closure $modifyBuilder = null;

    protected ?Closure $redirectAfter = null;

    protected bool $withoutModals = false;

    protected null|TableBuilderContract|FormBuilderContract|ActionButtonContract $resolvedComponent = null;

    /**
     * @var null|ListOf<Action>
     */
    protected ?ListOf $activeActions = null;

    public function disableOutside(): static
    {
        $this->outsideComponent = false;

        return $this->searchable(false);
    }

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

    public function getRedirectAfter(Model|int|null|string $parentId): ?string
    {
        if (! \is_null($this->redirectAfter)) {
            return (string) \call_user_func($this->redirectAfter, $parentId, $this);
        }

        if ($this->isAsync()) {
            return null;
        }

        return $this->getDefaultRedirect($parentId);
    }

    public function getDefaultRedirect(Model|int|null|string $parentId): ?string
    {
        /** @var ?CrudResourceContract $resource */
        $resource = $this->getNowOnResource() ?? $this->getCore()->getCrudRequest()->getResource();

        return $resource->getFormPageUrl($parentId);
    }

    /**
     * @param  list<ActionButtonContract>  $buttons
     */
    public function buttons(iterable $buttons): static
    {
        $this->buttons = ActionGroup::make($buttons);

        return $this;
    }

    public function getButtons(): ActionGroup
    {
        return $this->buttons ?? ActionGroup::make();
    }

    /**
     * @param  list<ActionButtonContract>  $buttons
     */
    public function indexButtons(array $buttons): static
    {
        $this->indexButtons = $buttons;

        return $this;
    }

    public function getIndexButtons(): array
    {
        return $this->indexButtons;
    }

    /**
     * @param  list<ActionButtonContract>  $buttons
     */
    public function formButtons(array $buttons): static
    {
        $this->formButtons = $buttons;

        return $this;
    }

    public function getFormButtons(): array
    {
        return $this->formButtons;
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
     * @param  Closure(TableBuilderContract $table, bool $preview): TableBuilderContract  $callback
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

        if ($this->isOutsideComponent()) {
            $this->isSearchable = false;
        }

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
        if (\is_null($this->getRelatedModel()?->getKey())) {
            return null;
        }

        if (! $this->isCreatable()) {
            return null;
        }

        $button = HasManyButton::for($this, button: $this->createButton);

        if (! \is_null($this->modifyCreateButton)) {
            $button = value($this->modifyCreateButton, $button, $this);
        }

        return $button->isSee()
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
     * Only for Preview
     * @throws Throwable
     */
    protected function prepareFields(): FieldsContract
    {
        if (! $this->hasFields()) {
            $fields = $this->getResource()->getIndexFields();

            $this->fields($fields->toArray());

            return $this->getFields();
        }

        /** @var Fields $fields */
        $fields = $this->getFields()->onlyFields(withWrappers: true);

        return $fields->indexFields();
    }

    /**
     * @throws Throwable
     */
    public function prepareClonedFields(): FieldsContract
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
        /** @var ModelResource $resource */
        $resource = clone $this->getResource()
            ->disableSaveQueryState();

        // If the records are already in memory (eager load) and there is no modifier, then we take the records from memory
        if (\is_null($this->modifyBuilder) && $this->getRelatedModel()?->relationLoaded($this->getRelationName()) === true) {
            $items = $this->toRelatedCollection();
        } else {
            $resource->disableQueryFeatures();

            $casted = $this->getRelatedModel();
            $relation = $casted?->{$this->getRelationName()}();

            /** @var Builder $query */
            $query = \is_null($this->modifyBuilder)
                ? $relation
                : value($this->modifyBuilder, $relation, $this);

            $resource->customQueryBuilder($query->limit($this->getLimit()));

            $items = $resource->getQuery()->get();
        }

        return TableBuilder::make(items: $items)
            ->fields($this->getFieldsOnPreview())
            ->cast($resource->getCaster())
            ->preview()
            ->simple()
            ->when(
                ! \is_null($this->modifyTable),
                fn (TableBuilderContract $tableBuilder) => value($this->modifyTable, $tableBuilder, true)
            );
    }

    /**
     * HasOne/HasMany mapper with updateOnPreview
     */
    private function getFieldsOnPreview(): Closure
    {
        return function () {
            $fields = $this->prepareClonedFields();

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
    protected function getTableValue(): TableBuilder
    {
        $items = $this->getValue();
        $resource = $this->getResource()->stopGettingItemFromUrl();

        // Need for assets
        $resource->getFormFields();

        $asyncUrl = moonshineRouter()->getEndpoints()->withRelation(
            'has-many.list',
            resourceItem: $this->getRelatedModel()?->getKey(),
            relation: $this->getRelationName(),
            resourceUri: $this->getNowOnResource()?->getUriKey(),
            pageUri: $this->getNowOnPage()?->getUriKey()
        );

        return TableBuilder::make(items: $items)
            ->async($asyncUrl)
            ->when(
                $this->isSearchable() && $this->getResource()->hasSearch(),
                static fn (TableBuilderContract $table): TableBuilderContract => $table->searchable()
            )
            ->name($this->getRelationName())
            ->fields($this->getFieldsOnPreview())
            ->cast($resource->getCaster())
            ->withNotFound()
            ->buttons($this->getItemButtons())
            ->when(
                ! \is_null($this->modifyTable),
                fn (TableBuilderContract $tableBuilder) => value($this->modifyTable, $tableBuilder, false)
            );
    }

    public function activeActions(Action ...$actions): static
    {
        $this->activeActions = new ListOf(Action::class, $actions);

        return $this;
    }

    public function withoutActions(Action ...$actions): static
    {
        $this->activeActions = (new ListOf(Action::class, [
            Action::CREATE,
            Action::VIEW,
            Action::UPDATE,
            Action::DELETE,
            Action::MASS_DELETE,
        ]))->except(...$actions);

        return $this;
    }

    protected function hasAction(Action ...$actions): bool
    {
        if (! $this->activeActions instanceof ListOf) {
            return true;
        }

        return Collection::make($actions)->every(fn (Action $action): bool => \in_array(
            $action,
            $this->activeActions->toArray(),
            true
        ));
    }

    /**
     * @throws Throwable
     */
    protected function getItemButtons(): array
    {
        /** @var ModelResource $resource */
        $resource = $this->getResource()->stopGettingItemFromUrl();

        $redirectAfter = $this->getRedirectAfter(
            $this->getRelatedModel()?->getKey()
        );

        $editButton = $this->editButton ?? HasManyButton::for($this, update: true);

        if (! \is_null($this->modifyEditButton)) {
            $editButton = value($this->modifyEditButton, $editButton, $this);
        }

        $detailButton = $resource->getDetailButton(
            modalName:  "has-many-modal-{$this->getResource()->getUriKey()}-{$this->getRelationName()}-{$this->getRelatedModel()?->getKey()}-detail",
            isSeparateModal: false
        );

        $deleteButton = $resource->getDeleteButton(
            componentName: $this->getRelationName(),
            redirectAfterDelete: $redirectAfter,
            isAsync: $this->isAsync(),
            modalName: "has-many-modal-{$this->getResource()->getUriKey()}-{$this->getRelationName()}-{$this->getRelatedModel()?->getKey()}-delete"
        );

        $massDeleteButton = $resource->getMassDeleteButton(
            componentName: $this->getRelationName(),
            redirectAfterDelete: $redirectAfter,
            isAsync: $this->isAsync(),
            modalName: "has-many-modal-{$this->getResource()->getUriKey()}-{$this->getRelationName()}-mass-delete"
        );

        if (! \is_null($this->modifyItemButtons)) {
            return \call_user_func(
                $this->modifyItemButtons,
                $detailButton,
                $editButton,
                $deleteButton,
                $massDeleteButton,
                $this,
            );
        }

        return array_filter([
            ...$this->getIndexButtons(),
            $this->hasAction(Action::VIEW) ? $detailButton : null,
            $this->hasAction(Action::UPDATE) ? $editButton : null,
            $this->hasAction(Action::DELETE) ? $deleteButton : null,
            $this->hasAction(Action::MASS_DELETE) ? $massDeleteButton : null,
        ]);
    }

    protected function prepareFill(array $raw = [], ?DataWrapperContract $casted = null): mixed
    {
        return null;
    }

    protected function resolveRawValue(): mixed
    {
        return $this->toRelatedCollection()
            ->map(fn (Model $item) => data_get($item, $this->getResourceColumn()))
            ->implode(';');
    }

    /**
     * @throws Throwable
     */
    protected function resolvePreview(): Renderable|string
    {
        if ($this->isRelatedLink()) {
            return $this->getRelatedLink()->render();
        }

        return $this->isModalMode()
            ? (string) $this->getModalButton(
                Components::make([$this->getTablePreview()]),
                $this->getLabel(),
                $this->getRelationName()
            )
            : $this->getTablePreview()->render();
    }

    /**
     * @throws Throwable
     */
    protected function resolveValue(): mixed
    {
        $resource = $this->getResource()
            ->disableSaveQueryState();

        $resource->setQueryParams(
            request()->only($resource->getQueryParamsKeys())
        );

        $casted = $this->getRelatedModel();
        $relation = $casted?->{$this->getRelationName()}();

        $resource->customQueryBuilder(
            \is_null($this->modifyBuilder)
                ? $relation
                : value($this->modifyBuilder, $relation, $this)
        );

        return $resource->getItems();
    }

    /**
     * @throws Throwable
     */
    public function getComponent(): ComponentContract
    {
        if (! \is_null($this->resolvedComponent)) {
            return $this->resolvedComponent;
        }

        return $this->resolvedComponent = $this->isRelatedLink()
            ? $this->getRelatedLink()
            : $this->getTableValue();
    }

    protected function resolveOnApply(): ?Closure
    {
        return static fn (Model $item): Model => $item;
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterDestroy(mixed $data): mixed
    {
        $this->getResource()
            ->getFormFields()
            ->onlyFields()
            ->each(fn (FieldContract $field): mixed => $field->setParent($this)->fillData($data)->afterDestroy($data));

        return $data;
    }

    public function isReactivitySupported(): bool
    {
        return false;
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return $this->isModalMode()
            ? $this->modalViewData()
            : $this->defaultViewData()
        ;
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function modalViewData(): array
    {
        /** @var Components<ComponentContract> $components */
        $components = new Components();
        /** @var Components<ComponentContract> $flexComponents */
        $flexComponents = new Components();

        if ($this->isCreatable()) {
            $flexComponents->add($this->getCreateButton());
        }

        if (! \is_null($this->buttons)) {
            $flexComponents->add($this->getButtons());
        }

        if ($flexComponents->isNotEmpty()) {
            $components->add(Flex::make($flexComponents)->justifyAlign('between'));
        }

        $components->add(LineBreak::make());
        $components->add($this->getComponent());

        return [
            'component' => $this->getModalButton(
                $components,
                $this->getLabel(),
                $this->getRelationName()
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    public function defaultViewData(): array
    {
        return [
            'component' => $this->getComponent(),
            'isCreatable' => $this->isCreatable(),
            'createButton' => $this->getCreateButton(),
            'buttons' => $this->getButtons(),
        ];
    }
}
