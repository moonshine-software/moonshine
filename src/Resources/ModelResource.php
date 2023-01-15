<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Leeto\MoonShine\Actions\Action;
use Leeto\MoonShine\Contracts\Resources\HasEntity;
use Leeto\MoonShine\Contracts\Resources\WithFields;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\Contracts\EntityContract;
use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Exceptions\ResourceException;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Filters\ModelFilter;
use Leeto\MoonShine\Metrics\Metric;
use Leeto\MoonShine\Traits\Resource\ResourceCrudRouter;
use Leeto\MoonShine\Traits\Resource\ResourceModelEvents;
use Leeto\MoonShine\Traits\Resource\ResourceModelPolicy;
use Leeto\MoonShine\Traits\Resource\ResourceModelQuery;
use Leeto\MoonShine\Traits\Resource\ResourceRouter;
use Leeto\MoonShine\Traits\WithUriKey;
use Leeto\MoonShine\Entities\ModelEntityBuilder;
use Leeto\MoonShine\Views\CrudDetailView;
use Leeto\MoonShine\Views\CrudFormView;
use Leeto\MoonShine\Views\CrudIndexView;
use Leeto\MoonShine\Views\Views;

abstract class ModelResource implements ResourceContract, WithFields, HasEntity
{
    use ResourceModelQuery;
    use ResourceModelPolicy;
    use ResourceModelEvents;
    use ResourceRouter;
    use ResourceCrudRouter;
    use WithUriKey;

    public static string $model;

    public static string $title = '';

    public string $column = 'id';

    protected int|string|null $entityId = null;

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
     * @return array<ModelFilter>
     */
    abstract public function filters(): array;

    /**
     * Get an array of additional actions performed on resource page
     *
     * @return Action[]
     */
    abstract public function actions(): array;

    abstract public function rowActions(Model $item): array;

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

    public function setEntityId(string|int $entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getEntityId(): string|int|null
    {
        return $this->entityId;
    }

    public function getDataInstance(): Model
    {
        return $this->getModel();
    }

    public function getData($id): ?Model
    {
        return $this->getDataInstance()
            ->newQuery()
            ->find($id);
    }

    public function title(): string
    {
        return static::$title;
    }

    public function column(): string
    {
        return $this->column;
    }

    public function entity(mixed $values): EntityContract
    {
        return (new ModelEntityBuilder($values))
            ->build()
            ->withActions($this->rowActions($values), $this->routeParam());
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

    public function views(): Views
    {
        return Views::make([
            CrudIndexView::class,
            CrudFormView::class,
            CrudDetailView::class,
        ]);
    }

    /**
     * @throws ResourceException
     */
    public function create(Model $item, array $values): Model
    {
        try {
            static::beforeCreating($item);

            $item->forceFill($values);

            if ($item->save()) {
                static::afterCreated($item);
            }
        } catch (QueryException $queryException) {
            throw ResourceException::queryError($queryException->getMessage());
        }

        return $item;
    }

    /**
     * @throws ResourceException
     */
    public function update(Model $item, array $values): Model
    {
        try {
            static::beforeUpdating($item);

            $item->forceFill($values);

            if ($item->save()) {
                static::afterUpdated($item);
            }
        } catch (QueryException $queryException) {
            throw ResourceException::queryError($queryException->getMessage());
        }

        return $item;
    }

    public function delete(Model $item): bool
    {
        static::beforeDeleting($item);

        return tap($item->delete(), fn() => static::afterDeleted($item));
    }

    public function massDelete(array $ids): bool
    {
        static::beforeMassDeleting($ids);

        return tap(
            $this->getModel()
                ->newModelQuery()
                ->whereIn($this->getModel()->getKeyName(), $ids)
                ->delete(),
            fn() => static::afterMassDeleted($ids)
        );
    }

    /**
     * @throws ResourceException
     */
    public function forceDelete(Model $item): bool
    {
        if(!$this->softDeletes()) {
            throw ResourceException::withoutSoftDeletes();
        }

        static::beforeForceDeleting($item);

        return tap($item->forceDelete(), fn() => static::afterForceDeleted($item));
    }

    /**
     * @throws ResourceException
     */
    public function restore(Model $item): bool
    {
        if(!$this->softDeletes()) {
            throw ResourceException::withoutSoftDeletes();
        }

        static::beforeRestoring($item);

        return tap($item->restore(), fn() => static::afterRestored($item));
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getEntityId(),
            'title' => $this->title(),
            'uri' => $this->uriKey(),
            'endpoint' => $this->route('index'),
            'policies' => $this->policies(),
            'softDeletes' => $this->softDeletes(),
        ];
    }
}
