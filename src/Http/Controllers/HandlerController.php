<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use MoonShine\Exceptions\ResourceException;
use MoonShine\MoonShineRequest;
use Symfony\Component\HttpFoundation\Response;

final class HandlerController extends MoonShineController
{
    public function __invoke(string $resourceUri, string $handlerUri, MoonShineRequest $request): Response
    {
        throw_if(
            ! $request->hasResource(),
            new ResourceException('Resource is required')
        );

        $handler = $request
            ->getResource()
            ?->getHandlers()
            ?->findByUri($handlerUri);

        if (! is_null($handler)) {
            return $handler
                ->setResource($request->getResource())
                ->handle();
        }

        return redirect(
            $request->getResource()->route('crud.index')
        );
    }
}
