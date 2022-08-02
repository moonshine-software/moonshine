<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Resources;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Stringable;
use Leeto\MoonShine\Actions\Action;
use Leeto\MoonShine\Contracts\Actions\ActionContract;
use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Contracts\Fields\Relationships\BelongsToRelation;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationship;
use Leeto\MoonShine\Contracts\Fields\Relationships\ManyToManyRelation;
use Leeto\MoonShine\Contracts\Renderable;
use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Decorations\Tab;
use Leeto\MoonShine\Exceptions\ResourceException;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Filters\Filter;
use Leeto\MoonShine\Metrics\Metric;
use Leeto\MoonShine\MoonShine;


abstract class Resource
{
    public static string $model;

    public static string $title = '';

    public static array $with = [];

    public static bool $withPolicy = false;

    public static string $orderField = 'id';

    public static string $orderType = 'DESC';

    public static int $itemsPerPage = 25;

    public static string $indexView = 'moonshine::index';

    public static string $createEditView = 'moonshine::create-edit';

    public string $titleField = '';

    /**
     * Get an array of validation rules for resource related model
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     *
     * @param  Model  $item
     * @return array
     */
    abstract function rules(Model $item): array;

    /**
     * Get an array of visible fields on resource page
     *
     * @return Field[]|Decoration[]
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

    public function indexView(): string
    {
        return static::$indexView;
    }

    public function createEditView(): string
    {
        return static::$createEditView;
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

    public function getModel(): Model
    {
        return new static::$model();
    }

    public function getRelationsValues(array $relations = null): array
    {
        return $this->fieldsRelationsValues(
            $this->getModel(),
            collect($relations ?? $this->getAllRelations())->flip()->undot()->toArray(),
            only: [BelongsTo::class, BelongsToMany::class]
        );
    }

    private function fieldsRelationsValues(
        Model $model,
        array $relations,
        array $values = null,
        array $only = [],
        string $prevKey = null
    ): array {
        $values = $values ?? [];
        foreach ($relations as $relation => $value) {
            $relatedModel = $model->{$relation}()->getRelated();

            $key = (string) str($prevKey ?? $relation)
                ->when($prevKey, fn(Stringable $str) => $str->append(".$relation"));

            if (empty($only) || in_array(get_class($model->{$relation}()), $only)) {
                $values[$key] = $relatedModel->all()->toArray();
            }

            if (is_array($value)) {
                $values = $this->fieldsRelationsValues($relatedModel, $value, $values, $only, $key ?? $relation);
            }
        }

        return $values;
    }

    public function getAllRelations(): array
    {
        return $this->fieldsRelations($this->fields());
    }

    private function fieldsRelations(array $fields, array $relations = null, string $prevKey = null): array
    {
        $relations = $relations ?? [];

        foreach ($fields as $field) {
            if ($field instanceof HasRelationship) {
                $key = (string) str($prevKey ?? $field->relation())
                    ->when($prevKey, fn(Stringable $str) => $str->append(".{$field->relation()}"));


                $relations[$key] = $key;

                if ($field instanceof HasFields && $field->getFields()) {
                    $relations = $this->fieldsRelations(
                        $field->getFields(),
                        $relations,
                        $key ?? $field->relation()
                    );
                }
            }
        }

        return $relations;
    }

    public function isWithPolicy(): bool
    {
        return static::$withPolicy;
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

    public function routeName(?string $action = null): string
    {
        return (string) str(config('moonshine.route.prefix'))
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
        return (string) str(static::class)
            ->classBasename()
            ->replace(['Resource'], '')
            ->append('Controller')
            ->whenContains(
                ['MoonShine'],
                fn(Stringable $str) => $str->prepend('Leeto\MoonShine\Http\Controllers\\'),
                fn(Stringable $str) => $str->prepend(MoonShine::namespace('\Controllers\\'))
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
    public function formFields(): Collection
    {
        return $this->getFields()
            ->filter(fn(Field $field) => $field->showOnForm);
    }

    /**
     * @return Collection<Renderable>
     */
    public function formElements(): Collection
    {
        # TODO Не все поля попадают

        $elements = [];

        foreach ($this->fields() as $element) {
            $elements = $this->resolveFormElements($element);
        }

        dd($elements);

        return collect($elements);
    }

    private function resolveFormElements($element)
    {
        $elements = [];

        if ($element instanceof BelongsToRelation || $element instanceof ManyToManyRelation) {
            # TODO вычилсять getRelated
            /*$element->setValues(
                $element->resolveRelatedValues($this->getRelationsValues()[$element->relation()] ?? [])
            );*/
        }

        $elements[] = $element->setParents();

        if (($element instanceof Tab || $element instanceof HasFields) && $element->hasFields()) {
            foreach ($element->getFields() as $child) {
                $elements[] = $this->resolveFormElements($child);
            }
        }

        return $elements;
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
                foreach ($item->getFields() as $field) {
                    if ($field instanceof Field) {
                        $fields[] = $field;
                    }
                }
            }
        }

        return collect($fields);
    }

    /**
     * @return Collection<Field>
     */
    public function indexFields(): Collection
    {
        return $this->getFields()
            ->filter(fn(Field $field) => $field->showOnIndex);
    }

    /**
     * @return Collection<Field>
     */
    public function exportFields(): Collection
    {
        return $this->getFields()
            ->filter(fn(Field $field) => $field->showOnExport);
    }

    public function fieldsLabels(): array
    {
        # TODO Рекурсивно по всем полям

        $labels = [];

        foreach ($this->getFields() as $field) {
            $labels[$field->field()] = $field->label();
        }

        return $labels;
    }

    /**
     * @return Collection<Field>
     */
    public function whenFields(): Collection
    {
        return collect($this->getFields())
            ->filter(fn(Field $field) => $field->showWhenState);
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

    public function paginate(): LengthAwarePaginator
    {
        return $this->query()->paginate(static::$itemsPerPage);
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

        return $query;
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
    public function save(Model $item, array $values): Model
    {
        try {
            $item->forceFill($values);
        } catch (QueryException $queryException) {
            throw new ResourceException($queryException->getMessage());
        }

        return $item;
    }

    public function delete(Model $item)
    {
    }

    public function forceDelete(Model $item)
    {
    }

    public function massDelete(Model $item)
    {
    }

    public function restore(Model $item)
    {
    }
}
