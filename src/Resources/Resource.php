<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use JsonSerializable;
use Leeto\MoonShine\Actions\Action;
use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Exceptions\ResourceException;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Filters\Filter;
use Leeto\MoonShine\Metrics\Metric;
use Leeto\MoonShine\Traits\Resource\ResourcePolicy;
use Leeto\MoonShine\Traits\Resource\ResourceQuery;
use Leeto\MoonShine\Traits\Resource\ResourceRouter;

abstract class Resource implements JsonSerializable
{
    use ResourceQuery;
    use ResourcePolicy;
    use ResourceRouter;

    public static string $model;

    public static string $title = '';

    public string $column = 'id';

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

    public function getModel(): Model
    {
        return new static::$model();
    }

    public function title(): string
    {
        return static::$title;
    }

    public function column(): string
    {
        return $this->column;
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

    public function fieldsCollection(): Fields
    {
        return Fields::make($this->fields());
    }

    /**
     * @throws ResourceException
     */
    public function create(Model $item, array $values): Model
    {
        try {
            if (method_exists($this, 'beforeCreating')) {
                call_user_func([$this, 'beforeCreating'], $item, $values);
            }

            $item->forceFill($values);
            $item->save();

            if (method_exists($this, 'afterCreated')) {
                call_user_func([$this, 'afterCreated'], $item);
            }
        } catch (QueryException $queryException) {
            throw new ResourceException($queryException->getMessage());
        }

        return $item;
    }

    /**
     * @throws ResourceException
     */
    public function update(Model $item, array $values): Model
    {
        try {
            $item->forceFill($values);
            $item->save();
        } catch (QueryException $queryException) {
            throw new ResourceException($queryException->getMessage());
        }

        return $item;
    }

    public function delete(Model $item): bool
    {
        return $item->delete();
    }

    public function massDelete(array $ids): bool
    {
        return $this->getModel()
            ->newModelQuery()
            ->whereIn($this->getModel()->getKeyName(), $ids)
            ->delete();
    }

    public function forceDelete(Model $item): bool
    {
        return $item->forceDelete();
    }

    public function restore(Model $item): bool
    {
        return $item->restore();
    }

    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title(),
            'uri' => $this->uriKey(),
            'softDeletes' => $this->softDeletes(),
        ];
    }
}
