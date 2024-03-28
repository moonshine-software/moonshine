<?php

declare(strict_types=1);

namespace MoonShine\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MoonShine\Pages\Crud\ErrorPage;

class MoonShineNotFoundException extends Exception
{
    public function render(Request $request): Response
    {
        return response(
            ErrorPage::make(
                Response::HTTP_NOT_FOUND,
                trans('moonshine::ui.404'),
            )
        )->setStatusCode(Response::HTTP_NOT_FOUND);
    }

    public static function pageNotFound(): self
    {
        return new self('Page not found');
    }
}
