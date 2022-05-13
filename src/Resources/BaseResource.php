<?php

namespace Leeto\MoonShine\Resources;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

use Leeto\MoonShine\Contracts\Components\ViewComponentContract;
use Leeto\MoonShine\Contracts\Fields\FieldHasRelationContract;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;

use Leeto\MoonShine\Decorations\Tab;
use Leeto\MoonShine\Exceptions\ResourceException;
use Leeto\MoonShine\Extensions\BaseExtension;
use Leeto\MoonShine\Fields\BaseField;

use Leeto\MoonShine\Filters\BaseFilter;
use Leeto\MoonShine\Traits\Resources\ExportTrait;
use Leeto\MoonShine\Traits\Resources\RouteTrait;
use Leeto\MoonShine\Traits\Resources\QueryTrait;

abstract class BaseResource implements ResourceContract
{
    use QueryTrait, RouteTrait, ExportTrait;

    public static string $model;

    public Model $item;

    public string $titleField = '';

    public static string $title = '';

    public static string $subtitle = '';

    public static array $actions = ['create', 'show', 'edit', 'delete'];

    public static array $with = [];

    public static bool $withPolicy = false;

    public static string $baseIndexView = 'moonshine::base.index';

    public static string $baseEditView = 'moonshine::base.form';

    protected static bool $system = false;

    abstract function rules(Model $item): array;

    abstract function fields(): array;

    abstract function search(): array;

    abstract function filters(): array;

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

    public function getActions(): array
    {
        return static::$actions;
    }

    public function isWithPolicy(): bool
    {
        return static::$withPolicy;
    }

    public function routeAlias(): string
    {
        return (string) str(static::class)
            ->classBasename()
            ->replace(['Resource'], '')
            ->plural()
            ->lower();
    }

    public function routeName(string|null $action = null): string
    {
        return (string) str(config('moonshine.route.prefix'))
            ->append('.')
            ->append($this->routeAlias())
            ->when($action, fn($str) => $str->append('.')->append($action));
    }

    public function controllerName(): string
    {
        return (string) str(static::class)
            ->classBasename()
            ->replace(['Resource'], '')
            ->append('Controller')
            ->when(
                static::$system,
                fn($str) => $str->prepend('\Leeto\MoonShine\Controllers\\'),
                fn($str) => $str->prepend('\\' . config('moonshine.route.namespace') . '\\')
            );
    }

    /* @return BaseField[] */
    public function getFields(): Collection
    {
        $fields = [];

        foreach ($this->fields() as $item) {
            if($item instanceof BaseField) {
                $fields[] = $item;
            } elseif($item instanceof Tab) {
                foreach ($item->fields() as $field) {
                    if($field instanceof BaseField) {
                        $fields[] = $field;
                    }
                }
            }
        }

        return collect($fields);
    }

    /* @return Tab[] */
    public function tabs(): Collection
    {
        return collect($this->fields())
            ->filter(fn ($item) => $item instanceof Tab);
    }

    /* @return BaseField[] */
    public function whenFields(): Collection
    {
        return collect($this->getFields())
            ->filter(fn (ViewComponentContract $field) => $field->showWhenState);
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

    /* @return BaseField[] */
    public function indexFields(): Collection
    {
        return $this->getFields()
            ->filter(fn (ViewComponentContract $field) => $field instanceof BaseField && $field->showOnIndex);
    }

    /* @return BaseField[] */
    public function formFields(): Collection
    {
        $fields = $this->extensionsFields();

        return $fields->merge($this->getFields()
            ->filter(fn (ViewComponentContract $field) => $field instanceof BaseField && $field->showOnForm));
    }

    /* @return BaseField[] */
    public function extensionsFields(): Collection
    {
        $fields = collect();

        foreach (app(BaseExtension::class) as $extension) {
            $fields = $fields->merge(
                collect($extension->fields())
                    ->filter(fn(ViewComponentContract $field) => $field instanceof BaseField && $field->showOnForm)
            );
        }

        return $fields;
    }

    /* @return BaseField[] */
    public function exportFields(): Collection
    {
        return $this->getFields()
            ->filter(fn (ViewComponentContract $field) => $field instanceof BaseField && $field->showOnExport);
    }

    public function fieldsLabels(): array
    {
        $labels = [];

        foreach ($this->formFields() as $field) {
            $labels[$field->label()] = $field->label();
        }

        return $labels;
    }

    public function getAssets(string $type): array
    {
        $assets = [];

        foreach ($this->getFields() as $field) {
            if($field->getAssets()) {
                $assets = array_merge($field->getAssets(), $assets);
            }
        }

        return $assets[$type] ?? [];
    }

    public function getFilter(string $filterName): BaseFilter|null
    {
        return collect($this->filters())->filter(function (BaseFilter $filter) use($filterName) {
            return $filter->field() == $filterName;
        })->first();
    }

    public function getField(string $fieldName): BaseField|null
    {
        return collect($this->getFields())->filter(function (BaseField $field) use($fieldName) {
            return $field->field() == $fieldName;
        })->first();
    }

    public function extensions($name, Model $item): string
    {
        $views = str('');

        if(app(BaseExtension::class)) {
            foreach (app(BaseExtension::class) as $extension) {
                if(method_exists($extension, $name)) {
                    $views->append($extension->{$name}($item));
                }
            }
        }

        return (string) $views;
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

    public function save(Model $item): Model
    {
        try {
            foreach ($this->formFields() as $field) {
                if(!$field instanceof FieldHasRelationContract
                    || (!$field->isRelationHasOne() && $field->isRelationToOne())) {
                    $item = $field->save($item);
                }
            }

            if($item->save()) {
                foreach ($this->formFields() as $field) {
                    if($field instanceof FieldHasRelationContract && (!$field->isRelationToOne() || $field->isRelationHasOne())) {
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

    public function renderField(ViewComponentContract $field, Model $item): Factory|View|Application
    {
        return $this->_render($field, $item);
    }

    public function renderFilter(ViewComponentContract $field, Model $item): Factory|View|Application
    {
        return $this->_render($field, $item);
    }

    public function renderDecoration(ViewComponentContract $decoration, Model $item): Factory|View|Application
    {
        return view($decoration->getView(), [
            'resource' => $this,
            'item' => $item,
            'decoration' => $decoration,
        ]);
    }

    protected function _render(ViewComponentContract $field, Model $item): Factory|View|Application
    {
        if ($field instanceof FieldHasRelationContract) {
            $related = $item->{$field->relation()}()->getRelated();

            $field->options(
                $related->pluck($field->resourceTitleField(), $related->getKeyName())
                    ->toArray()
            );
        }

        return view($field->getView(), [
            'resource' => $this,
            'item' => $item,
            'field' => $field,
        ]);
    }

}