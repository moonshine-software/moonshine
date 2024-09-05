<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use MoonShine\Core\Exceptions\ResourceException;
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
        throw_if(
            ! $request->hasResource(),
            ResourceException::required()
        );

        $handler = $request
            ->getResource()
            ?->getHandlers()
            ?->findByUri($handlerUri);

        if (! is_null($handler)) {
            return $handler->handle();
        }

        return redirect(
            $request->getResource()?->getUrl() ?? moonshineRouter()->getEndpoints()->home()
        );
    }
}
