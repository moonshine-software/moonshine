<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Resources;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Leeto\MoonShine\Actions\Action;
use Leeto\MoonShine\Actions\Actions;
use Leeto\MoonShine\Actions\FiltersAction;
use Leeto\MoonShine\BulkActions\BulkAction;
use Leeto\MoonShine\Contracts\Actions\ActionContract;
use Leeto\MoonShine\Contracts\ResourceRenderable;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Exceptions\ResourceException;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Filters\Filter;
use Leeto\MoonShine\Filters\Filters;
use Leeto\MoonShine\FormActions\FormAction;
use Leeto\MoonShine\FormComponents\FormComponent;
use Leeto\MoonShine\ItemActions\ItemAction;
use Leeto\MoonShine\Metrics\Metric;
use Leeto\MoonShine\QueryTags\QueryTag;
use Leeto\MoonShine\Traits\Resource\ResourceCrudRouter;
use Leeto\MoonShine\Traits\Resource\ResourceModelPolicy;
use Leeto\MoonShine\Traits\Resource\ResourceModelQuery;
use Leeto\MoonShine\Traits\Resource\ResourceRouter;
use Leeto\MoonShine\Traits\WithUriKey;
use Throwable;

abstract class Resource implements ResourceContract
{
    use ResourceRouter;
    use ResourceCrudRouter;
    use ResourceModelPolicy;
    use ResourceModelQuery;
    use WithUriKey;

    public static string $model;

    public static string $title = '';

    public static string $subTitle = '';

    public static array $activeActions = ['create', 'show', 'edit', 'delete'];

    public static string $baseIndexView = 'moonshine::crud.index';

    public static string $baseEditView = 'moonshine::crud.form';

    public static string $baseShowView = 'moonshine::crud.show';

    public string $titleField = '';

    protected static bool $system = false;

    protected ?Model $item = null;

    protected bool $createInModal = false;

    protected bool $editInModal = false;

    protected bool $precognition = false;

    protected string $relatedColumn = '';

    protected string|int $relatedKey = '';

    protected bool $previewMode = false;

    /**
     * Alias for route of resource.
     * @var string
     */
    protected string $routAlias = '';

    /**
     * Get an array of validation rules for resource related model
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     *
     * @param  Model  $item
     * @return array
     */
    abstract public function rules(Model $item): array;

    /**
     * Get an array of visible fields on resource page
     *
     * @return Field[]|Decoration[]
     */
    abstract public function fields(): array;

    /**
     * Get an array of filters displayed on resource index page
     *
     * @return Filter[]
     */
    abstract public function filters(): array;

    /**
     * Get an array of additional actions performed on resource page
     *
     * @return Action[]
     */
    abstract public function actions(): array;

    /**
     * Get an array of fields which will be used for search on resource index page
     *
     * @return array
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

    /**
     * Get an array of custom bulk actions
     *
     * @return array<int, BulkAction>
     */
    public function bulkActions(): array
    {
        return [];
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

    /**
     * Get an array of custom form actions
     *
     * @return array<int, FormAction>
     */
    public function formActions(): array
    {
        return [];
    }

    /**
     * Get an array of custom form actions
     *
     * @return array<int, FormComponent>
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
     * @param  Model  $item
     * @param  int  $index
     * @return string
     */
    public function trClass(Model $item, int $index): string
    {
        return 'default';
    }

    /**
     * Customize table td class
     *
     * @param  Model  $item
     * @param  int  $index
     * @param  int  $cell
     * @return string
     */
    public function tdClass(Model $item, int $index, int $cell): string
    {
        return 'default';
    }

    /**
     * Customize table row style
     *
     * @param  Model  $item
     * @param  int  $index
     * @return string
     */
    public function trStyles(Model $item, int $index): string
    {
        return '';
    }

    /**
     * Customize table td style
     *
     * @param  Model  $item
     * @param  int  $index
     * @param  int  $cell
     * @return string
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

    public function getItem(): ?Model
    {
        return $this->item;
    }

    public function setItem(Model $item): void
    {
        $this->item = $item;
    }

    public function getModel(): Model
    {
        return new static::$model();
    }

    public function getActiveActions(): array
    {
        return static::$activeActions;
    }

    public function isSystem(): bool
    {
        return static::$system;
    }

    public function isInModal(): bool
    {
        return $this->isEditInModal() || $this->isCreateInModal();
    }

    public function isCreateInModal(): bool
    {
        return $this->createInModal;
    }

    public function isEditInModal(): bool
    {
        return $this->editInModal;
    }

    public function isPrecognition(): bool
    {
        return $this->precognition
            || $this->isInModal()
            || $this->isRelatable();
    }

    public function precognitionMode(): self
    {
        $this->precognition = true;

        return $this;
    }

    /**
     * @return Actions<ActionContract>
     */
    public function getActions(): Actions
    {
        $actions = Actions::make($this->actions());

        if (! $this->getFilters()->isEmpty()) {
            $actions = $actions->mergeIfNotExists(
                FiltersAction::make(trans('moonshine::ui.filters'))
            );
        }

        return $actions->onlyVisible()
            ->map(fn (Action $action) => $action->setResource($this));
    }

    public function hasMassAction(): bool
    {
        return ! $this->isPreviewMode() && (
            count($this->bulkActions()) || (
                $this->can('massDelete') && in_array('delete', $this->getActiveActions(), true)
            )
        );
    }

    /**
     * @return Fields<Field|Decoration>
     * @throws Throwable
     */
    public function getFields(): Fields
    {
        return Fields::make($this->fields());
    }

    /**
     * @throws Throwable
     */
    public function getField(string $fieldName): ?Field
    {
        return $this->getFields()->findFieldByColumn($fieldName);
    }

    /**
     * @return Filters<Filter>
     */
    public function getFilters(): Filters
    {
        return Filters::make($this->filters());
    }

    /**
     * @throws Throwable
     */
    public function getFilter(string $filterName): ?Filter
    {
        return $this->getFilters()->findFilterByColumn($filterName);
    }

    /**
     * Determine if this resource uses soft deletes.
     *
     * @return bool
     */
    public function softDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive(static::$model), true);
    }

    public function relatable(string $column, string|int $key): self
    {
        $this->relatedColumn = $column;
        $this->relatedKey = $key;
        $this->createInModal = true;
        $this->editInModal = true;

        return $this->precognitionMode();
    }

    public function isRelatable(): bool
    {
        return ($this->relatedColumn && $this->relatedKey);
    }

    public function previewMode(): self
    {
        $this->previewMode = true;

        return $this;
    }

    public function isPreviewMode(): bool
    {
        return $this->previewMode;
    }

    public function relatedColumn(): string
    {
        return $this->relatedColumn;
    }

    public function relatedKey(): string|int
    {
        return $this->relatedKey;
    }

    /**
     * @throws Throwable
     */
    public function validate(Model $item): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        return Validator::make(
            request()->all(),
            $this->rules($item),
            trans('moonshine::validation'),
            $this->getFields()->extractLabels()
        );
    }

    public function massDelete(array $ids)
    {
        if (method_exists($this, 'beforeMassDeleting')) {
            $this->beforeMassDeleting($ids);
        }

        return tap(
            $this->getModel()
                ->newModelQuery()
                ->whereIn($this->getModel()->getKeyName(), $ids)
                ->delete(),
            function () use ($ids) {
                if (method_exists($this, 'afterMassDeleted')) {
                    $this->afterMassDeleted($ids);
                }
            }
        );
    }

    public function delete(Model $item): bool
    {
        if (method_exists($this, 'beforeDeleting')) {
            $this->beforeDeleting($item);
        }

        return tap($item->delete(), function () use ($item) {
            if (method_exists($this, 'afterDeleted')) {
                $this->afterDeleted($item);
            }
        });
    }

    /**
     * @throws ResourceException|Throwable
     */
    public function save(
        Model $item,
        ?Collection $fields = null,
        ?array $saveData = null
    ): Model {
        $fields = $fields ?? $this->getFields()->formFields();

        try {
            $fields->each(fn ($field) => $field->beforeSave($item));

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

                if ($wasRecentlyCreated && method_exists($this, 'afterCreated')) {
                    $this->afterCreated($item);
                }

                if (! $wasRecentlyCreated && method_exists($this, 'afterUpdated')) {
                    $this->afterUpdated($item);
                }
            }
        } catch (QueryException $queryException) {
            throw new ResourceException($queryException->getMessage());
        }

        return $item;
    }

    protected function saveItem(Model $item, Field $field, ?array $saveData = null): Model
    {
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

    public function renderComponent(ResourceRenderable $component, Model $item, int $level = 0): View
    {
        if ($component instanceof Field && $component->hasRelationship()
            && ($component->belongToOne() || $component->manyToMany())) {
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
