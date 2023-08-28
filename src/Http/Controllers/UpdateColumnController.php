<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Http\Response;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Fields\Fields;
use MoonShine\Http\Requests\Resources\UpdateColumnFormRequest;

class UpdateColumnController extends MoonShineController
{
    public function __invoke(UpdateColumnFormRequest $request): Response
    {
        $resource = $request->getResource();

        try {
            $resource->save(
                $resource->getItemOrFail(),
                Fields::make([$request->getField()])
            );
        } catch (ResourceException $e) {
            throw_if(! app()->isProduction(), $e);
            report_if(app()->isProduction(), $e);

            return response($e->getMessage());
        }

        return response()->noContent();
    }
}
