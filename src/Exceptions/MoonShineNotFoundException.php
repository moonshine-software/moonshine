<?php

declare(strict_types=1);

namespace MoonShine\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MoonShine\Pages\ErrorPage;

class MoonShineNotFoundException extends MoonShineException
{
    public function render(Request $request): Response
    {
        $page = moonshineConfig()->getPage(
            'error',
            ErrorPage::class,
            code: Response::HTTP_NOT_FOUND,
            message: trans('moonshine::ui.404'),
        );

        return response($page)->setStatusCode(Response::HTTP_NOT_FOUND);
    }

    public static function pageNotFound(): self
    {
        return new self('Page not found');
    }
}
