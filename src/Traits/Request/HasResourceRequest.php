<?php

namespace MoonShine\Traits\Request;

use MoonShine\Contracts\Resources\ResourceContract;

trait HasResourceRequest
{
    public function getResource(): ?ResourceContract
    {
        return memoize(fn (): ?ResourceContract => moonshine()->getResourceFromUriKey(
            $this->getResourceUri()
        )?->boot());
    }

    public function hasResource(): bool
    {
        return ! is_null($this->getResource());
    }

    public function getResourceUri(): ?string
    {
        return $this->route('resourceUri');
    }
}
