<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use MoonShine\Actions\Action;
use MoonShine\Actions\FiltersAction;
use MoonShine\Actions\MassActions;
use MoonShine\BulkActions\BulkAction;
use MoonShine\Contracts\Actions\ActionContract;
use MoonShine\Contracts\ResourceRenderable;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Decorations\Decoration;
use MoonShine\DetailComponents\DetailComponent;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\FormElement;
use MoonShine\Fields\StackFields;
use MoonShine\Filters\Filter;
use MoonShine\Filters\Filters;
use MoonShine\FormActions\FormAction;
use MoonShine\FormComponents\FormComponent;
use MoonShine\ItemActions\ItemAction;
use MoonShine\ItemActions\ItemActions;
use MoonShine\Metrics\Metric;
use MoonShine\QueryTags\QueryTag;
use MoonShine\Traits\Resource\ResourceCrudRouter;
use MoonShine\Traits\Resource\ResourceModelPolicy;
use MoonShine\Traits\Resource\ResourceModelQuery;
use MoonShine\Traits\Resource\ResourceRouter;
use MoonShine\Traits\WithIsNowOnRoute;
use MoonShine\Traits\WithUriKey;
use Throwable;

abstract class Resource implements ResourceContract
{
    use ResourceRouter;
    use ResourceCrudRouter;
    use ResourceModelPolicy;
    use ResourceModelQuery;
    use WithUriKey;
    use WithIsNowOnRoute;

    public static string $model;

    public static string $title = '';

    public static string $subTitle = '';

    public static array $activeActions = ['create', 'show', 'edit', 'delete'];

    public static string $baseIndexView = 'moonshine::crud.index';

    public static string $baseEditView = 'moonshine::crud.form';

    public static string $baseShowView = 'moonshine::crud.show';
    protected static bool $system = false;
    public string $titleField = '';
    protected string $itemsView = 'moonshine::crud.shared.table';
    protected string $formView = 'moonshine::crud.shared.form';
    protected string $detailView = 'moonshine::crud.shared.detail-card';
    protected ?Model $item = null;

    protected bool $showInModal = false;

    protected bool $createInModal = false;

    protected bool $editInModal = false;

    protected bool $precognition = false;

    protected bool $previewMode = false;

    protected bool $relatable = false;

    protected string $routeAfterSave = 'index';

    /**
     * Alias for route of resource.
     */
    protected string $routAlias = '';

    /**
     * Get an array of fields which will be used for search on resource index page
     */
    abstract public function search(): array;

    /**
     * Get an array of filter scopes, which will be applied on resource index page
     *
     * @see https://laravel.com/docs/eloquent#writing-global-scopes
     *
     * @return Scope[]
     */
    public function scopes(): array
    {
        return [];
    }

    /**
     * Get an array of metrics which will be displayed on resource index page
     *
     * @return Metric[]
     */
    public function metrics(): array
    {
        return [];
    }

    public function bulkActionsCollection(): MassActions
    {
        return MassActions::make($this->bulkActions());
    }

    /**
     * Get an array of custom bulk actions
     *
     * @return array<int, BulkAction>
     */
    public function bulkActions(): array
    {
        return [];
    }

    public function itemActionsCollection(): ItemActions
    {
        return ItemActions::make($this->itemActions());
    }

    /**
     * Get an array of custom item actions
     *
     * @return array<int, ItemAction>
     */
    public function itemActions(): array
    {
        return [];
    }

    public function formActionsCollection(): ItemActions
    {
        return ItemActions::make($this->formActions());
    }

    /**
     * Get an array of custom form actions
     *
     * @return array<int, FormAction>
     */
    public function formActions(): array
    {
        return [];
    }

    public function componentsCollection(): ResourceComponents
    {
        return ResourceComponents::make($this->components());
    }

    /**
     * Get an array of custom form actions
     *
     * @return array<int, FormComponent|DetailComponent>
     */
    public function components(): array
    {
        return [];
    }

    /**
     * Get an array of custom form actions
     *
     * @return array<int, QueryTag>
     */
    public function queryTags(): array
    {
        return [];
    }

    /**
     * Customize table row class
     *
     *
     * @deprecated $item argument is deprecated and will be removed in future versions,
     * use $this->getItem() instead of $item
     *
     */
    public function trClass(Model $item, int $index): string
    {
        return 'default';
    }

    /**
     * Customize table td class
     *
     *
     * @deprecated $item argument is deprecated and will be removed in future versions,
     * use $this->getItem() instead of $item
     *
     */
    public function tdClass(Model $item, int $index, int $cell): string
    {
        return 'default';
    }

    /**
     * Customize table row style
     *
     *
     * @deprecated $item argument is deprecated and will be removed in future versions,
     * use $this->getItem() instead of $item
     *
     */
    public function trStyles(Model $item, int $index): string
    {
        return '';
    }

    /**
     * Customize table td style
     *
     *
     * @deprecated $item argument is deprecated and will be removed in future versions,
     * use $this->getItem() instead of $item
     *
     */
    public function tdStyles(Model $item, int $index, int $cell): string
    {
        return '';
    }

    public function baseShowView(): string
    {
        return static::$baseShowView;
    }

    public function baseIndexView(): string
    {
        return static::$baseIndexView;
    }

    public function baseEditView(): string
    {
        return static::$baseEditView;
    }

    public function itemsView(): string
    {
        return $this->itemsView;
    }

    public function formView(): string
    {
        return $this->formView;
    }

    public function detailView(): string
    {
        return $this->detailView;
    }

    public function title(): string
    {
        return static::$title;
    }

    public function subTitle(): string
    {
        return static::$subTitle;
    }

    public function titleField(): string
    {
        return $this->titleField;
    }

    public function setTitleField(string $titleField): void
    {
        $this->titleField = $titleField;
    }

    public function isSystem(): bool
    {
        return static::$system;
    }

    public function isShowInModal(): bool
    {
        return $this->showInModal;
    }

    public function isPrecognition(): bool
    {
        return $this->precognition
            || $this->isInCreateOrEditModal()
            || $this->isRelatable();
    }

    public function isInCreateOrEditModal(): bool
    {
        return $this->isEditInModal()
            || $this->isCreateInModal();
    }

    public function isEditInModal(): bool
    {
        return $this->editInModal;
    }

    public function isCreateInModal(): bool
    {
        return $this->createInModal;
    }

    public function isRelatable(): bool
    {
        return $this->relatable;
    }

    /**
     * @return MassActions<ActionContract>
     */
    public function getActions(): MassActions
    {
        $actions = MassActions::make($this->actions());

        if (! $this->getFilters()->isEmpty()) {
            $actions = $actions->mergeIfNotExists(
                FiltersAction::make(trans('moonshine::ui.filters'))
            );
        }

        return $actions->onlyVisible()
            ->map(fn (Action $action): Action => $action->setResource($this));
    }

    /**
     * Get an array of additional actions performed on resource page
     *
     * @return Action[]
     */
    abstract public function actions(): array;

    /**
     * @return Filters<Filter>
     */
    public function getFilters(): Filters
    {
        return Filters::make($this->filters());
    }

    /**
     * Get an array of filters displayed on resource index page
     *
     * @return Filter[]
     */
    abstract public function filters(): array;

    public function hasMassAction(): bool
    {
        return ! $this->isPreviewMode() && (
            count($this->bulkActions()) || (
                $this->can('massDelete') && in_array(
                    'delete',
                    $this->getActiveActions(),
                    true
                )
            )
        );
    }

    public function isPreviewMode(): bool
    {
        return $this->previewMode;
    }

    public function getActiveActions(): array
    {
        return static::$activeActions;
    }

    /**
     * @throws Throwable
     */
    public function getField(string $fieldName): ?Field
    {
        return $this->getFields()
            ->onlyFields()
            ->unwrapFields(StackFields::class)
            ->findByColumn($fieldName);
    }

    /**
     * @return Fields<Field|Decoration>
     * @throws Throwable
     */
    public function getFields(): Fields
    {
        $fields = Fields::make($this->fields());

        $fields->withCurrentResource($this);
        $fields->withParents();
        $fields->prepareAttributes();

        return $fields;
    }

    /**
     * Get an array of visible fields on resource page
     *
     * @return Field[]|Decoration[]
     */
    abstract public function fields(): array;

    /**
     * @throws Throwable
     */
    public function getFilter(string $filterName): ?Filter
    {
        return $this->getFilters()
            ->onlyFields()
            ->unwrapFields(StackFields::class)
            ->findByColumn($filterName);
    }

    /**
     * Determine if this resource uses soft deletes.
     */
    public function softDeletes(): bool
    {
        return isset(class_uses_recursive($this->getModel())[SoftDeletes::class]);
    }

    public function relatable(): self
    {
        $this->relatable = true;
        $this->createInModal = true;
        $this->editInModal = true;
        $this->showInModal = true;

        return $this->precognitionMode();
    }

    public function precognitionMode(): self
    {
        $this->precognition = true;

        return $this;
    }

    public function previewMode(): self
    {
        $this->previewMode = true;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function validate(Model $item): ValidatorContract
    {
        return Validator::make(
            request()->all(),
            $this->rules($item),
            array_merge(
                trans('moonshine::validation'),
                $this->validationMessages()
            ),
            $this->getFields()->extractLabels()
        );
    }

    public function prepareForValidation(): void
    {
    }

    /**
     * Get an array of validation rules for resource related model
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    abstract public function rules(Model $item): array;

    /**
     * Get custom messages for validator errors
     *
     * @return array<string, string|array<string, string>>
     */
    public function validationMessages(): array
    {
        return [];
    }

    public function massDelete(array $ids): void
    {
        if (method_exists($this, 'beforeMassDeleting')) {
            $this->beforeMassDeleting($ids);
        }

        $this->transformToResources(
            $this->getModel()
                ->newModelQuery()
                ->whereIn($this->getModel()->getKeyName(), $ids)
                ->get()
        )
            ->each(fn ($resource) => $resource->delete($resource->getItem()));

        if (method_exists($this, 'afterMassDeleted')) {
            $this->afterMassDeleted($ids);
        }
    }

    public function getModel(): Model
    {
        return new static::$model();
    }

    public function delete(Model $item): bool
    {
        if (method_exists($this, 'beforeDeleting')) {
            $this->beforeDeleting($item);
        }

        $this->getFields()->formFields()->each(
            fn ($field) => $field->afterDelete($item)
        );

        return tap($item->delete(), function () use ($item): void {
            if (method_exists($this, 'afterDeleted')) {
                $this->afterDeleted($item);
            }
        });
    }

    public function getItem(): ?Model
    {
        return $this->item;
    }

    public function setItem(Model $item): self
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @throws ResourceException|Throwable
     */
    public function save(
        Model $item,
        ?Collection $fields = null,
        ?array $saveData = null
    ): Model {
        $fields ??= $this->getFields()->formFields();

        try {
            $fields->each(fn (Field $field) => $field->beforeSave($item));

            if (! $item->exists && method_exists($this, 'beforeCreating')) {
                $this->beforeCreating($item);
            }

            if ($item->exists && method_exists($this, 'beforeUpdating')) {
                $this->beforeUpdating($item);
            }

            foreach ($fields as $field) {
                if (! $field->hasRelationship() || $field->belongToOne()) {
                    $item = $this->saveItem($item, $field, $saveData);
                }
            }

            if ($item->save()) {
                $wasRecentlyCreated = $item->wasRecentlyCreated;

                foreach ($fields as $field) {
                    if ($field->hasRelationship() && ! $field->belongToOne()) {
                        $item = $this->saveItem($item, $field, $saveData);
                    }
                }

                $item->save();

                $fields->each(fn ($field) => $field->afterSave($item));

                if ($wasRecentlyCreated && method_exists(
                    $this,
                    'afterCreated'
                )) {
                    $this->afterCreated($item);
                }

                if (! $wasRecentlyCreated && method_exists(
                    $this,
                    'afterUpdated'
                )) {
                    $this->afterUpdated($item);
                }

                $this->setItem($item);
            }
        } catch (QueryException $queryException) {
            throw new ResourceException($queryException->getMessage());
        }

        return $item;
    }

    protected function saveItem(
        Model $item,
        Field $field,
        ?array $saveData = null
    ): Model {
        if (! $field->isCanSave()) {
            return $item;
        }

        if (is_null($saveData)) {
            return $field->save($item);
        }

        if (isset($saveData[$field->field()])) {
            $item->{$field->field()} = $saveData[$field->field()];
        }

        return $item;
    }

    public function renderComponent(
        ResourceRenderable $component,
        Model $item,
        int $level = 0
    ): View {
        if ($component instanceof FormElement
            && $component->hasRelatedValues()
            && ! $component->values()) {
            $component->setValues($component->relatedValues($item));
        }

        return view($component->getView(), [
            'resource' => $this,
            'item' => $item,
            'element' => $component,
            'level' => $level,
        ]);
    }
}
