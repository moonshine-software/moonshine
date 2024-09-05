<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MoonShine\Core\Exceptions\MoonShineException;
use MoonShine\Laravel\Pages\ErrorPage;

class MoonShineNotFoundException extends MoonShineException
{
    public function report(): bool
    {
        return false;
    }

    public function render(Request $request): Response
    {
        $page = moonshineConfig()->getPage(
            'error',
            ErrorPage::class,
        )
            ->code(Response::HTTP_NOT_FOUND)
            ->message(__('moonshine::ui.404'));

        return response($page)->setStatusCode(Response::HTTP_NOT_FOUND);
    }

    public static function pageNotFound(): static
    {
        return new static('Page not found');
    }
}
