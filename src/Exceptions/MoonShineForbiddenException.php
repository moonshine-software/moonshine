<?php

declare(strict_types=1);

namespace MoonShine\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MoonShineForbiddenException extends Exception
{
    public function render(Request $request): Response
    {
        return response()->view('moonshine::errors.index', [
            'code' => 403,
            'message' => trans('moonshine::ui.403'),
        ])->setStatusCode(403);
    }
}
