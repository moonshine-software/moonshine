<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use MoonShine\Resources\ModelResource;

trait ResourceWithParent
{
    protected ?string $parentId = null;

    abstract protected function getParentResource(): ModelResource;

    abstract protected function getParentRelationName(): string;

    protected function getParentId()
    {
        if(! is_null($this->parentId)) {
            return $this->parentId;
        }

        $parentResource = $this->getParentResource();

        $relationName = $this->getParentRelationName();

        if(moonshineRequest()->getResourceUri() === $parentResource->uriKey()) {
            return $this->parentId = request('resourceItem');
        }

        if(request($parentKey = $this->getModel()
            ?->{$relationName}()
            ->getForeignKeyName()))
        {
            return $this->parentId = request($parentKey);
        }

        if(is_null($this->getItem())) {
            return $this->parentId = moonshineRequest()->getParentResourceId();
        }

        $parentKey = $this->getItem()->{$relationName}()->getOwnerKeyName();

        return $this->parentId = $this->getItem()?->{$relationName}->{$parentKey};
    }
}