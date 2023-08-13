<?php

declare(strict_types=1);

namespace MoonShine;

use MoonShine\Pages\Page;
use MoonShine\Pages\Pages;
use MoonShine\Resources\Resource;
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
        string|Resource $resource,
        string|Page|null $page = null,
        array $params = [],
        bool $redirect = false
    ): RedirectResponse|string {
        $resource = $resource instanceof Resource
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
