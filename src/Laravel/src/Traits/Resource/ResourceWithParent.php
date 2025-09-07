<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

/**
 * @phpstan-ignore trait.unused
 */
trait ResourceWithParent
{
    protected null|string|int $parentId = null;

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

        $parentKey = $this->getModel()
            ?->{$relationName}()
            ->getForeignKeyName();

        if (! \is_null($parentKey) && request()->filled($parentKey)) {
            return $this->parentId = request()->getScalar($parentKey);
        }

        if (\is_null($this->getItem())) {
            return $this->parentId = $this->getCore()->getCrudRequest()->getParentResourceId();
        }

        $parentKey = $this->getItem()?->{$relationName}()->getOwnerKeyName();

        return $this->parentId = $this->getItem()?->{$relationName}->{$parentKey};
    }
}
