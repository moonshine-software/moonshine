<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Request;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\ResourceContract;

/**
 * @template T of ResourceContract
 */
trait HasResourceRequest
{
    /**
     * @return null|T
     */
    public function getResource(): ?CrudResourceContract
    {
        if (\is_null($this->getResourceUri())) {
            return null;
        }

        return memoize(fn (): ?ResourceContract => moonshine()->getResources()->findByUri(
            $this->getResourceUri()
        ));
    }

    public function hasResource(): bool
    {
        return ! \is_null($this->getResource());
    }

    public function getResourceUri(): ?string
    {
        return $this->route('resourceUri');
    }
}
