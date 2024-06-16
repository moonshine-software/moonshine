<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

trait ResourceWithParent
{
    protected null|string|int $parentId = null;

    abstract public function getItemID(): int|string|null;

    abstract protected function getParentResourceClassName(): string;

    abstract protected function getParentRelationName(): string;

    protected function getParentId(): null|string|int
    {
        if(! is_null($this->parentId)) {
            return $this->parentId;
        }

        $parentResource = moonshine()
            ->getResources()->findByClass(
                $this->getParentResourceClassName()
            );

        if(is_null($parentResource)) {
            return null;
        }

        $relationName = $this->getParentRelationName();

        if(moonshineRequest()->getResourceUri() === $parentResource->getUriKey()) {
            return $this->parentId = $this->getItemID();
        }

        if(request($parentKey = $this->getModel()
            ?->{$relationName}()
            ->getForeignKeyName())) {
            return $this->parentId = request($parentKey);
        }

        if(is_null($this->getItem())) {
            return $this->parentId = moonshineRequest()->getParentResourceId();
        }

        $parentKey = $this->getItem()?->{$relationName}()->getOwnerKeyName();

        return $this->parentId = $this->getItem()?->{$relationName}->{$parentKey};
    }
}
