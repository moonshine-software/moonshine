<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Fields\Fields;
use MoonShine\Http\Requests\Resources\UpdateColumnFormRequest;

class UpdateColumnController extends BaseController
{
    public function __invoke(UpdateColumnFormRequest $request): Response
    {
        $request->merge([
            $request->field()->column() => $request->get('value'),
        ]);

        try {
            $request->getResource()->save(
                $request->getItemOrFail(),
                Fields::make([$request->field()])
            );
        } catch (ResourceException $e) {
            throw_if(! app()->isProduction(), $e);
            report_if(app()->isProduction(), $e);

            return response($e->getMessage());
        }

        return response()->noContent();
    }
}
