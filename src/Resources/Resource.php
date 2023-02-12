<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Resources;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Leeto\MoonShine\Actions\Action;
use Leeto\MoonShine\Actions\FiltersAction;
use Leeto\MoonShine\BulkActions\BulkAction;
use Leeto\MoonShine\Contracts\Actions\ActionContract;
use Leeto\MoonShine\Contracts\HtmlViewable;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Decorations\Tabs;
use Leeto\MoonShine\Exceptions\ResourceException;
use Leeto\MoonShine\Extensions\Extension;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Filters\Filter;
use Leeto\MoonShine\FormActions\FormAction;
use Leeto\MoonShine\FormActions\QueryTag;
use Leeto\MoonShine\FormComponents\FormComponent;
use Leeto\MoonShine\ItemActions\ItemAction;
use Leeto\MoonShine\Metrics\Metric;
use Leeto\MoonShine\MoonShine;
use Throwable;

abstract class Resource implements ResourceContract
{
    public static string $model;

    public static string $title = '';

    public static string $subTitle = '';

    public static array $with = [];

    public static bool $withPolicy = false;

    public static string $orderField = 'id';

    public static string $orderType = 'DESC';

    public static int $itemsPerPage = 25;

    public static array $activeActions = ['create', 'show', 'edit', 'delete'];

    public static string $baseIndexView = 'moonshine::base.index';

    public static string $baseEditView = 'moonshine::base.form';

    public string $titleField = '';

    protected static bool $system = false;

    protected ?Model $item = null;

    protected bool $createInModal = false;

    protected bool $editInModal = false;

    protected bool $precognition = true;

    protected string $relatedColumn = '';

    protected string|int $relatedKey = '';

    protected bool $previewMode = false;

    protected ?Builder $customBuilder = null;

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

    public function isWithPolicy(): bool
    {
        return static::$withPolicy;
    }

    public function isSystem(): bool
    {
        return static::$system;
    }

    public function isCreateInModal(): bool
    {
        return $this->createInModal;
    }

    public function isEditInModal(): bool
    {
        return $this->editInModal;
    }

    /**
     * Take route of resource from alias or composite from resource and table names.
     * @return string
     */
    public function routeAlias(): string
    {
        return (string) ($this->routAlias ?
            str($this->routAlias)
                ->lcfirst()
                ->squish() :
            str(static::class)
                ->classBasename()
                ->replace(['Resource'], '')
                ->plural()
                ->lcfirst());
    }

    public function routeParam(): string
    {
        return (string) str($this->routeAlias())->singular();
    }

    public function routeName(string|null $action = null): string
    {
        return (string) str(config('moonshine.route.prefix'))
            ->append('.')
            ->append($this->routeAlias())
            ->when($action, fn ($str) => $str->append('.')->append($action));
    }

    public function currentRoute(array $query = []): string
    {
        return request()->url()
            . ($query ? '?' . http_build_query($query) : '');
    }

    public function route(string $action = null, int|string $id = null, array $query = []): string
    {
        if (empty($query) && Cache::has("moonshine_query_{$this->routeAlias()}")) {
            parse_str(
                Cache::get("moonshine_query_{$this->routeAlias()}", ''),
                $query
            );
        }

        if ($this->isRelatable()) {
            $query['relatable_mode'] = 1;
        }

        return route(
            $this->routeName($action),
            $id ? array_merge([$this->routeParam() => $id], $query) : $query
        );
    }

    public function controllerName(): string
    {
        return (string) str(static::class)
            ->classBasename()
            ->replace(['Resource'], '')
            ->append('Controller')
            ->when(
                $this->isSystem(),
                fn ($str) => $str->prepend('Leeto\MoonShine\Http\Controllers\\'),
                fn ($str) => $str->prepend(MoonShine::namespace('\Controllers\\'))
            );
    }

    public function isPrecognition(): bool
    {
        return $this->precognition;
    }

    public function precognitionMode(): self
    {
        $this->precognition = true;

        return $this;
    }

    /**
     * @return Collection<ActionContract>
     */
    public function getActions(): Collection
    {
        $actions = collect();

        $hasFilters = false;

        foreach ($this->actions() as $action) {
            if ($action instanceof FiltersAction) {
                $hasFilters = true;
            }

            $actions->add($action->setResource($this));
        }

        if (! $hasFilters && ! empty($this->filters())) {
            $actions->add(
                FiltersAction::make(trans('moonshine::ui.filters'))
                    ->setResource($this)
            );
        }

        return $actions;
    }


    /**
     * @return Collection<Field>
     * @throws Throwable
     */
    public function getFields(): Collection
    {
        $fields = [];

        $this->withdrawFields($this->fields(), $fields);

        return collect($fields);
    }

    /**
     * @throws Throwable
     */
    private function withdrawFields($fieldsOrDecorations, array &$fields)
    {
        foreach ($fieldsOrDecorations as $fieldOrDecoration) {
            if ($fieldOrDecoration instanceof Field) {
                $fields[] = $fieldOrDecoration;
            } elseif ($fieldOrDecoration instanceof Tabs) {
                foreach ($fieldOrDecoration->tabs() as $tab) {
                    $this->withdrawFields($tab->fields(), $fields);
                }
            } elseif ($fieldOrDecoration instanceof Decoration) {
                $this->withdrawFields($fieldOrDecoration->fields(), $fields);
            }
        }
    }

    /**
     * @return Collection<Filter>
     */
    public function getFilters(): Collection
    {
        return collect($this->filters());
    }

    /**
     * @return Collection<Field>
     * @throws Throwable
     */
    public function whenFields(): Collection
    {
        return collect($this->getFields())
            ->filter(fn (Field $field) => $field->showWhenState)
            ->values();
    }

    /**
     * @throws Throwable
     */
    public function whenFieldNames(): Collection
    {
        $names = [];

        foreach ($this->whenFields() as $field) {
            $names[$field->showWhenField] = $field->showWhenField;
        }

        return collect($names);
    }

    public function isWhenConditionField(string $name): bool
    {
        return $this->whenFieldNames()->has($name);
    }

    /**
     * @return Collection<Field>
     * @throws Throwable
     */
    public function indexFields(): Collection
    {
        return $this->getFields()
            ->filter(fn (Field $field) => $field->showOnIndex)
            ->values();
    }

    /**
     * @return Collection
     */
    public function formComponents(): Collection
    {
        return collect($this->fields())->map(function ($component) {
            if ($component instanceof Field) {
                return $component->setParents();
            }

            return $component;
        });
    }

    /**
     * @return Collection
     * @throws Throwable
     */
    public function relatableFormComponents(): Collection
    {
        return $this->getFields()
            ->filter(fn (Field $field) => $field->isResourceModeField())
            ->values()
            ->map(fn (Field $field) => $field->setParents())
        ;
    }

    /**
     * @return Collection<Field>
     * @throws Throwable
     */
    public function formFields(): Collection
    {
        $fields = $this->extensionsFields();

        return $fields->merge(
            $this->getFields()
                ->filter(fn (Field $field) => $field->showOnForm)
                ->values()
        );
    }

    /**
     * @return Collection<Field>
     */
    public function extensionsFields(): Collection
    {
        $fields = collect();

        foreach (app(Extension::class) as $extension) {
            $fields = $fields->merge(
                collect($extension->fields())
                    ->filter(fn (Field $field) => $field->showOnForm)
                    ->values()
            );
        }

        return $fields;
    }

    /**
     * @return Collection<Field>
     * @throws Throwable
     */
    public function exportFields(): Collection
    {
        return $this->getFields()
            ->filter(fn (Field $field) => $field->showOnExport)
            ->values();
    }

    /**
     * @return Collection<Field>
     * @throws Throwable
     */
    public function importFields(): Collection
    {
        return $this->getFields()
            ->filter(fn (Field $field) => $field->useOnImport)
            ->values();
    }

    /**
     * @throws Throwable
     */
    public function fieldsLabels(): array
    {
        $labels = [];

        foreach ($this->formFields() as $field) {
            $labels[$field->field()] = $field->label();
        }

        return $labels;
    }

    public function getFilter(string $filterName): Filter|null
    {
        return collect($this->getFilters())->filter(function (Filter $filter) use ($filterName) {
            return $filter->field() == $filterName;
        })->first();
    }

    /**
     * @throws Throwable
     */
    public function getField(string $fieldName): Field|null
    {
        return collect($this->getFields())->filter(function (Field $field) use ($fieldName) {
            return $field->field() == $fieldName;
        })->first();
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

    public function extensions($name, Model $item): string
    {
        $views = str('');

        if (app(Extension::class)) {
            foreach (app(Extension::class) as $extension) {
                if (method_exists($extension, $name)) {
                    $views = $views->append($extension->{$name}($item));
                }
            }
        }

        return (string) $views;
    }

    public function relatable(string $column, string|int $key): self
    {
        $this->relatedColumn = $column;
        $this->relatedKey = $key;
        $this->createInModal = true;
        $this->editInModal = true;

        return $this;
    }

    public function isRelatable(): bool
    {
        return $this->relatedColumn && $this->relatedKey;
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

    public function all(): Collection
    {
        return $this->query()->get();
    }

    public function paginate(): LengthAwarePaginator
    {
        return $this->query()
            ->paginate(static::$itemsPerPage)
            ->appends(request()->except('page'));
    }

    public function customBuilder(Builder $builder)
    {
        $this->customBuilder = $builder;
    }

    public function query(): Builder
    {
        $query = $this->customBuilder ?? $this->getModel()->query();

        if (static::$with) {
            $query = $query->with(static::$with);
        }

        if ($this->isRelatable()) {
            return $query
                ->where($this->relatedColumn(), $this->relatedKey())
                ->orderBy(static::$orderField, static::$orderType);
        }

        if ($this->scopes()) {
            foreach ($this->scopes() as $scope) {
                $query = $query->withGlobalScope($scope::class, $scope);
            }
        }

        if (request()->has('search') && count($this->search())) {
            foreach ($this->search() as $field) {
                $query = $query->orWhere(
                    $field,
                    'LIKE',
                    '%'.request('search').'%'
                );
            }
        }

        if (request()->has('filters') && count($this->filters())) {
            foreach ($this->filters() as $filter) {
                $query = $filter->getQuery($query);
            }
        }

        if (request()->has('order')) {
            $query = $query->orderBy(
                request('order.field'),
                request('order.type')
            );
        } else {
            $query = $query->orderBy(static::$orderField, static::$orderType);
        }

        Cache::forget("moonshine_query_{$this->routeAlias()}");
        Cache::remember("moonshine_query_{$this->routeAlias()}", now()->addHours(2), function () {
            return request()->getQueryString();
        });

        return $query;
    }

    public function validate(Model $item): \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
    {
        return Validator::make(
            request()->all(),
            $this->rules($item),
            trans('moonshine::validation'),
            $this->fieldsLabels()
        );
    }

    public function gateAbilities(): array
    {
        return [
            'viewAny', 'view', 'create', 'update', 'delete', 'massDelete', 'restore', 'forceDelete',
        ];
    }

    public function can(string $ability, Model $item = null): bool
    {
        $user = auth(config('moonshine.auth.guard'))->user();

        if ($user->moonshineUserPermission
            && (! $user->moonshineUserPermission->permissions->has(get_class($this))
                || ! isset($user->moonshineUserPermission->permissions[get_class($this)][$ability]))) {
            return false;
        }

        if (! $this->isWithPolicy()) {
            return true;
        }

        return Gate::forUser($user)
            ->allows($ability, $item ?? $this->getModel());
    }

    public function massDelete(array $ids): void
    {
        if (method_exists($this, 'beforeMassDeleting')) {
            call_user_func([$this, 'beforeMassDeleting'], $ids);
        }

        $this->getModel()
            ->newModelQuery()
            ->whereIn($this->getModel()->getKeyName(), $ids)
            ->delete();

        if (method_exists($this, 'afterMassDeleted')) {
            call_user_func([$this, 'afterMassDeleted'], $ids);
        }
    }

    public function delete(Model $item): void
    {
        if (method_exists($this, 'beforeDeleting')) {
            call_user_func([$this, 'beforeDeleting'], $item);
        }

        $item->delete();

        if (method_exists($this, 'afterDeleted')) {
            call_user_func([$this, 'afterDeleted'], $item);
        }
    }

    /**
     * @throws ResourceException|Throwable
     */
    public function save(Model $item, ?Collection $fields = null, ?array $saveData = null): Model
    {
        $fields = $fields ?? $this->formFields();

        try {
            $fields->each(fn ($field) => $field->beforeSave($item));

            if (! $item->exists) {
                if (method_exists($this, 'beforeCreating')) {
                    call_user_func([$this, 'beforeCreating'], $item);
                }
            }

            if ($item->exists) {
                if (method_exists($this, 'beforeUpdating')) {
                    call_user_func([$this, 'beforeUpdating'], $item);
                }
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

                if ($wasRecentlyCreated) {
                    if (method_exists($this, 'afterCreated')) {
                        call_user_func([$this, 'afterCreated'], $item);
                    }
                }

                if (! $wasRecentlyCreated) {
                    if (method_exists($this, 'afterUpdated')) {
                        call_user_func([$this, 'afterUpdated'], $item);
                    }
                }
            }
        } catch (QueryException $queryException) {
            throw new ResourceException($queryException->getMessage());
        }

        return $item;
    }

    protected function saveItem(Model $item, Field $field, ?array $saveData = null): Model
    {
        if (is_null($saveData)) {
            return $field->save($item);
        }

        if (isset($saveData[$field->field()])) {
            $item->{$field->field()} = $saveData[$field->field()];
        }

        return $item;
    }

    public function renderField(HtmlViewable $field, Model $item, int $level = 0): Factory|View|Application
    {
        return $this->_render($field, $item, $level);
    }

    public function renderFilter(HtmlViewable $field, Model $item): Factory|View|Application
    {
        return $this->_render($field, $item);
    }

    public function renderDecoration(HtmlViewable $decoration, Model $item): Factory|View|Application
    {
        return view($decoration->getView(), [
            'resource' => $this,
            'item' => $item,
            'decoration' => $decoration,
        ]);
    }

    public function renderMetric(HtmlViewable $metric): Factory|View|Application
    {
        return view($metric->getView(), [
            'resource' => $this,
            'item' => $metric,
        ]);
    }

    public function renderFormComponent(HtmlViewable $formComponent, Model $item): Factory|View|Application
    {
        return view($formComponent->getView(), [
            'resource' => $this,
            'item' => $item,
            'component' => $formComponent,
        ]);
    }

    public function isMassAction(): bool
    {
        return ! $this->isPreviewMode() && (
            count($this->bulkActions()) || (
                $this->can('massDelete') && in_array('delete', $this->getActiveActions())
            )
        );
    }

    protected function _render(HtmlViewable $field, Model $item, int $level = 0): Factory|View|Application
    {
        if ($field->hasRelationship() && ($field->belongToOne() || $field->manyToMany())) {
            $field->setValues($field->relatedValues($item));
        }

        return view($field->getView(), [
            'resource' => $this,
            'item' => $item,
            'field' => $field,
            'level' => $level,
        ]);
    }
}
