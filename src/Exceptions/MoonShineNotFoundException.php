<?php

declare(strict_types=1);

namespace MoonShine\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MoonShineNotFoundException extends Exception
{
    public function render(Request $request): Response
    {
        return response()->view('moonshine::errors.404', [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => trans('moonshine::ui.404'),
        ])->setStatusCode(Response::HTTP_NOT_FOUND);
    }
}
