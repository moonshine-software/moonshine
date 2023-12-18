<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use MoonShine\Exceptions\ResourceException;
use MoonShine\MoonShineRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class HandlerController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function __invoke(string $resourceUri, string $handlerUri, MoonShineRequest $request): Response
    {
        throw_if(
            ! $request->hasResource(),
            ResourceException::required()
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
