<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Request;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Core\Exceptions\ResourceException;

/**
 * @template T of CrudResourceContract
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

        /** @var T|null $resource */
        $resource = memoize(fn (): ?ResourceContract => moonshine()->getResources()->findByUri(
            $this->getResourceUri()
        ));

        return $resource;
    }

    /** @return T */
    public function getResourceOrFail(): CrudResourceContract
    {
        return $this->getResource() ?? throw ResourceException::notDeclared();
    }

    /** @phpstan-assert-if-true T $this->getResource() */
    public function hasResource(): bool
    {
        return ! \is_null($this->getResource());
    }

    public function getResourceUri(): ?string
    {
        return $this->route('resourceUri');
    }
}
