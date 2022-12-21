<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Resources;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
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
use Leeto\MoonShine\Decorations\Tab;
use Leeto\MoonShine\Exceptions\ResourceException;
use Leeto\MoonShine\Extensions\Extension;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Filters\Filter;
use Leeto\MoonShine\ItemActions\ItemAction;
use Leeto\MoonShine\Metrics\Metric;
use Leeto\MoonShine\MoonShine;

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
     * @return Field[]
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

    public function routeAlias(): string
    {
        return (string) str(static::class)
            ->classBasename()
            ->replace(['Resource'], '')
            ->plural()
            ->lcfirst();
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

    public function route(string $action = null, int|string $id = null, array $query = []): string
    {
        if (empty($query) && Cache::has("moonshine_query_{$this->routeAlias()}")) {
            parse_str(
                Cache::get("moonshine_query_{$this->routeAlias()}", ''),
                $query
            );
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
     */
    public function getFields(): Collection
    {
        $fields = [];

        foreach ($this->fields() as $item) {
            if ($item instanceof Field) {
                $fields[] = $item;
            } elseif ($item instanceof Tab) {
                foreach ($item->fields() as $field) {
                    if ($field instanceof Field) {
                        $fields[] = $field;
                    }
                }
            }
        }

        return collect($fields);
    }

    /**
     * @return Collection<Filter>
     */
    public function getFilters(): Collection
    {
        return collect($this->filters());
    }

    /**
     * @return Collection<Tab>
     */
    public function tabs(): Collection
    {
        return collect($this->fields())
            ->filter(fn ($item) => $item instanceof Tab);
    }

    /**
     * @return Collection<Field>
     */
    public function whenFields(): Collection
    {
        return collect($this->getFields())
            ->filter(fn (HtmlViewable $field) => $field instanceof Field && $field->showWhenState);
    }

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
     */
    public function indexFields(): Collection
    {
        return $this->getFields()
            ->filter(fn (HtmlViewable $field) => $field instanceof Field && $field->showOnIndex);
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
     * @return Collection<Field>
     */
    public function formFields(): Collection
    {
        $fields = $this->extensionsFields();

        return $fields->merge(
            $this->getFields()
                ->filter(fn (HtmlViewable $field) => $field instanceof Field && $field->showOnForm)
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
                    ->filter(fn (HtmlViewable $field) => $field instanceof Field && $field->showOnForm)
            );
        }

        return $fields;
    }

    /**
     * @return Collection<Field>
     */
    public function exportFields(): Collection
    {
        return $this->getFields()
            ->filter(fn (HtmlViewable $field) => $field instanceof Field && $field->showOnExport);
    }

    /**
     * @return Collection<Field>
     */
    public function importFields(): Collection
    {
        return $this->getFields()
            ->filter(fn (HtmlViewable $field) => $field instanceof Field && $field->useOnImport);
    }

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

    public function query(): Builder
    {
        $query = $this->getModel()->query();

        if ($this->scopes()) {
            foreach ($this->scopes() as $scope) {
                $query = $query->withGlobalScope($scope::class, $scope);
            }
        }

        if (static::$with) {
            $query = $query->with(static::$with);
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

    public function validate(Model $item): array
    {
        return Validator::validate(
            request()->all(),
            $this->rules($item),
            trans('moonshine::validation'),
            $this->fieldsLabels()
        );
    }

    public function can(string $ability, Model $item = null): bool
    {
        if (! $this->isWithPolicy()) {
            return true;
        }

        return Gate::forUser(auth(config('moonshine.auth.guard'))->user())
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
     * @throws ResourceException
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

    protected function saveItem(Model $item, Field $field, ?array $saveData = null)
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

    protected function _render(HtmlViewable $field, Model $item, int $level = 0): Factory|View|Application
    {
        if ($field->hasRelationship()) {
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
