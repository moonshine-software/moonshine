<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Contracts\Resource\HasHandlersContract;
use MoonShine\Laravel\MoonShineRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class HandlerController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function __invoke(string $resourceUri, string $handlerUri, MoonShineRequest $request): Response
    {
        $resource = $request->getResource();

        throw_if(
            ! $resource,
            ResourceException::required()
        );

        if (! $resource instanceof HasHandlersContract) {
            throw new ResourceException('Resource with HasHandlersContract required');
        }

        $handler = $resource
            ->getHandlers()
            ->findByUri($handlerUri);

        if (! is_null($handler)) {
            return $handler->handle();
        }

        return redirect(
            $request->getResource()?->getUrl() ?? moonshineRouter()->getEndpoints()->home()
        );
    }
}
