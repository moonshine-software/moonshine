<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Resources;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

use Leeto\MoonShine\Actions\Action;
use Leeto\MoonShine\Contracts\Actions\ActionContract;
use Leeto\MoonShine\Contracts\RenderableContract;
use Leeto\MoonShine\Contracts\Fields\HasRelationshipContract;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;

use Leeto\MoonShine\Decorations\Tab;
use Leeto\MoonShine\Exceptions\ResourceException;
use Leeto\MoonShine\Extensions\Extension;
use Leeto\MoonShine\Fields\Field;

use Leeto\MoonShine\Filters\Filter;
use Leeto\MoonShine\Metrics\Metric;
use Leeto\MoonShine\MoonShine;

abstract class Resource implements ResourceContract
{
    public static string $model;

    public static string $title = '';

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

    protected Model $item;

    /**
     * Get an array of validation rules for resource related model
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     *
     * @param  Model  $item
     *
     * @return array
     */
    abstract function rules(Model $item): array;

    /**
     * Get an array of visible fields on resource page
     *
     * @return Field[]
     */
    abstract function fields(): array;

    /**
     * Get an array of filters displayed on resource index page
     *
     * @return Filter[]
     */
    abstract function filters(): array;

    /**
     * Get an array of additional actions performed on resource page
     *
     * @return Action[]
     */
    abstract function actions(): array;

    /**
     * Get an array of fields which will be used for search on resource index page
     *
     * @return array
     */
    abstract function search(): array;

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

    public function titleField(): string
    {
        return $this->titleField;
    }

    public function setTitleField(string $titleField): void
    {
        $this->titleField = $titleField;
    }

    public function getItem(): Model
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
        return (string)str(static::class)
            ->classBasename()
            ->replace(['Resource'], '')
            ->plural()
            ->lcfirst();
    }

    public function routeParam(): string
    {
        return (string)str($this->routeAlias())->singular();
    }

    public function routeName(string|null $action = null): string
    {
        return (string)str(config('moonshine.route.prefix'))
            ->append('.')
            ->append($this->routeAlias())
            ->when($action, fn($str) => $str->append('.')->append($action));
    }

    public function route(string $action, int $id = null, array $query = []): string
    {
        return route(
            $this->routeName($action),
            $id ? array_merge([$this->routeParam() => $id], $query) : $query
        );
    }

    public function controllerName(): string
    {
        return (string)str(static::class)
            ->classBasename()
            ->replace(['Resource'], '')
            ->append('Controller')
            ->when(
                $this->isSystem(),
                fn($str) => $str->prepend('Leeto\MoonShine\Http\Controllers\\'),
                fn($str) => $str->prepend(MoonShine::namespace('\Controllers\\'))
            );
    }

    /**
     * @return Collection<ActionContract>
     */
    public function getActions(): Collection
    {
        $actions = collect();

        foreach ($this->actions() as $action) {
            $actions->add($action->setResource($this));
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
            ->filter(fn($item) => $item instanceof Tab);
    }

    /**
     * @return Collection<Field>
     */
    public function whenFields(): Collection
    {
        return collect($this->getFields())
            ->filter(fn(RenderableContract $field) => $field instanceof Field && $field->showWhenState);
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
            ->filter(fn(RenderableContract $field) => $field instanceof Field && $field->showOnIndex);
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
                ->filter(fn(RenderableContract $field) => $field instanceof Field && $field->showOnForm)
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
                    ->filter(fn(RenderableContract $field) => $field instanceof Field && $field->showOnForm)
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
            ->filter(fn(RenderableContract $field) => $field instanceof Field && $field->showOnExport);
    }

    public function fieldsLabels(): array
    {
        $labels = [];

        foreach ($this->formFields() as $field) {
            $labels[$field->field()] = $field->label();
        }

        return $labels;
    }

    public function getAssets(string $type): array
    {
        $assets = ['js' => [], 'css' => []];

        foreach ($this->getFields() as $field) {
            if ($field->getAssets()) {
                $assets = array_merge_recursive($field->getAssets(), $assets);
            }
        }

        foreach ($this->metrics() as $metric) {
            if ($metric->getAssets()) {
                $assets = array_merge_recursive($metric->getAssets(), $assets);
            }
        }

        $assets['js'] = array_unique($assets['js']);
        $assets['css'] = array_unique($assets['css']);

        return $assets[$type] ?? [];
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

        return (string)$views;
    }

    public function all(): Collection
    {
        return $this->query()->get();
    }

    public function paginate(): LengthAwarePaginator
    {
        return $this->query()->paginate(static::$itemsPerPage);
    }

    public function query(): Builder
    {
        $query = $this->getModel()->newModelQuery();

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
        if (!$this->isWithPolicy()) {
            return true;
        }

        return Gate::forUser(auth(config('moonshine.auth.guard'))->user())
            ->allows($ability, $item ?? $this->getModel());
    }

    /**
     * @throws ResourceException
     */
    public function save(Model $item): Model
    {
        try {
            foreach ($this->formFields() as $field) {
                if (!$field instanceof HasRelationshipContract
                    || (!$field->isRelationHasOne() && $field->isRelationToOne())) {
                    $item = $field->save($item);
                }
            }

            if ($item->save()) {
                foreach ($this->formFields() as $field) {
                    if ($field instanceof HasRelationshipContract && (!$field->isRelationToOne() || $field->isRelationHasOne())) {
                        $item = $field->save($item);
                    }
                }

                $item->save();
            }
        } catch (QueryException $queryException) {
            throw new ResourceException($queryException->getMessage());
        }

        return $item;
    }

    public function renderField(RenderableContract $field, Model $item, int $level = 0): Factory|View|Application
    {
        return $this->_render($field, $item, $level);
    }

    public function renderFilter(RenderableContract $field, Model $item): Factory|View|Application
    {
        return $this->_render($field, $item);
    }

    public function renderDecoration(RenderableContract $decoration, Model $item): Factory|View|Application
    {
        return view($decoration->getView(), [
            'resource' => $this,
            'item' => $item,
            'decoration' => $decoration,
        ]);
    }

    public function renderMetric(RenderableContract $metric): Factory|View|Application
    {
        return view($metric->getView(), [
            'resource' => $this,
            'item' => $metric
        ]);
    }

    protected function _render(RenderableContract $field, Model $item, int $level = 0): Factory|View|Application
    {
        if ($field instanceof HasRelationshipContract) {
            $field->options($field->relatedOptions($item));
        }

        return view($field->getView(), [
            'resource' => $this,
            'item' => $item,
            'field' => $field,
            'level' => $level,
        ]);
    }
}
