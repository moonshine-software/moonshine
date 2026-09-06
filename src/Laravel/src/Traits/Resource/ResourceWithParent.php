<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Laravel\Resources\ModelResource;

/** @phpstan-require-extends ModelResource */
trait ResourceWithParent
{
    protected null|string|int $parentId = null;

    /** @return class-string<ResourceContract> */
    abstract protected function getParentResourceClassName(): string;

    abstract protected function getParentRelationName(): string;

    protected function getParentId(): null|string|int
    {
        if (! \is_null($this->parentId)) {
            return $this->parentId;
        }

        $parentResource = $this->getCore()
            ->getResources()->findByClass(
                $this->getParentResourceClassName()
            );

        if (\is_null($parentResource)) {
            return null;
        }

        $relationName = $this->getParentRelationName();


        if ($this->getCore()->getCrudRequest()->getResourceUri() === $parentResource->getUriKey()) {
            return $this->parentId = $this->getCore()->getCrudRequest()->getItemID();
        }

        $parentKey = $this->resolveParentRelation($this->getModel())->getForeignKeyName();

        if (request()->filled($parentKey)) {
            return $this->parentId = $this->resolveParentKey(request()->getScalar($parentKey));
        }

        $item = $this->getItem();

        if ($item === null) {
            return $this->parentId = $this->getCore()->getCrudRequest()->getParentResourceId();
        }

        $parentKey = $this->resolveParentRelation($item)->getOwnerKeyName();

        return $this->parentId = $this->resolveParentKey(data_get($item, "$relationName.$parentKey"));
    }

    /** @return BelongsTo<Model, Model> */
    private function resolveParentRelation(Model $model): BelongsTo
    {
        $relation = $model->{$this->getParentRelationName()}();

        if (! $relation instanceof BelongsTo) {
            throw new \LogicException('The parent resource relation must be a BelongsTo relation.');
        }

        return $relation;
    }

    private function resolveParentKey(mixed $key): int|string|null
    {
        if ($key !== null && ! \is_int($key) && ! \is_string($key)) {
            throw new \TypeError('Expected an integer or string parent key, got ' . get_debug_type($key));
        }

        return $key;
    }
}
