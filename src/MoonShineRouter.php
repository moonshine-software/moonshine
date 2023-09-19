<?php

declare(strict_types=1);

namespace MoonShine;

use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Pages\Page;
use MoonShine\Pages\Pages;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class MoonShineRouter
{
    public static function to(string $name, array $params = []): string
    {
        return route(
            str($name)
                ->remove('moonshine.')
                ->prepend('moonshine.')
                ->value(),
            $params
        );
    }

    public static function to_page(
        string|ResourceContract|null $resource,
        string|Page|null $page = null,
        array $params = [],
        bool $redirect = false
    ): RedirectResponse|string {
        if(is_null($resource)) {
            return MoonShine::getPageFromUriKey(
                is_string($page) ? self::uriKey($page) : $page->uriKey()
            )->url();
        }

        $resource = $resource instanceof ResourceContract
            ? $resource
            : new $resource();

        $route = $resource->getPages()
            ->when(
                is_null($page),
                static fn (Pages $pages) => $pages->first(),
                static fn (Pages $pages): ?Page => $pages->findByUri(
                    $page instanceof Page
                        ? $page->uriKey()
                        : self::uriKey($page)
                ),
            )->route($params);

        return $redirect
            ? redirect($route)
            : $route;
    }

    public static function uriKey(string $class): string
    {
        return str($class)
            ->classBasename()
            ->kebab()
            ->value();
    }
}
