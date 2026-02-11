<?php

declare(strict_types=1);

namespace MoonShine\Laravel;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Core\AbstractEndpoints;
use MoonShine\Core\Exceptions\EndpointException;
use MoonShine\Support\Enums\PageType;
use MoonShine\Support\UriKey;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

/**
 * @extends AbstractEndpoints<CrudResourceContract>
 */
final readonly class MoonShineEndpoints extends AbstractEndpoints
{
    /**
     * @param  class-string<PageContract>|PageContract|null  $page
     * @param  class-string<ResourceContract>|ResourceContract|null  $resource
     * @param  array<string, string|int|float|null>  $params
     * @param  array<string, mixed>  $extra
     *
     * @throws Throwable
     */
    public function toPage(
        string|PageContract|null $page = null,
        string|ResourceContract|null $resource = null,
        array $params = [],
        array $extra = [],
    ): string|RedirectResponse {
        $redirect = $extra['redirect'] ?? false;
        $fragment = $extra['fragment'] ?? null;

        if (\is_array($fragment)) {
            $fragment = implode(',', array_map(
                static fn ($key, $value): string => "$key:$value",
                array_keys($fragment),
                $fragment
            ));
        }

        if ($fragment !== null && $fragment !== '') {
            $params += ['_fragment-load' => $fragment];
        }

        if (\is_null($page) && \is_null($resource)) {
            throw EndpointException::pageOrResourceRequired();
        }

        if (! \is_null($resource)) {
            $targetResource = $resource instanceof ResourceContract
                ? $resource
                : moonshine()->getResources()->findByClass($resource);

            if (\is_null($page)) {
                return $targetResource->getUrl();
            }

            $pageUri = $page instanceof PageContract
                ? $page->getUriKey()
                : (new UriKey($page))->generate();

            /**
             * Because from the resource we call the method with default CRUD pages, which can be replaced with custom ones
             * @example toPage(FormPage::class, $resource) -> CustomFormPage
             */
            $targetPage = $targetResource?->getPages()?->findByUri($pageUri);

            if (! $targetPage instanceof PageContract) {
                $pageType = PageType::getTypeFromUri($pageUri);

                $targetPage = $pageType instanceof PageType
                    ? $targetResource?->getPages()->findByType($pageType)
                    : null;
            }
        } elseif ($page instanceof PageContract) {
            $targetPage = $page;
        } else {
            /** @var ?ResourceContract $targetResource */
            $targetResource = moonshine()->getResources()->first(
                static fn (ResourceContract $resource): bool => $resource->getPages()->findByClass($page) instanceof PageContract
            );
            $targetPage = $targetResource?->getPages()?->findByClass($page);

            $targetPage ??= moonshine()->getPages()->findByClass($page);
        }

        if (! $targetPage instanceof PageContract) {
            throw EndpointException::pageRequired();
        }

        return $redirect
            ? redirect($targetPage->getRoute($params))
            : $targetPage->getRoute($params);
    }

    public function home(): string
    {
        if ($url = moonshineConfig()->getHomeUrl()) {
            return $url;
        }

        $route = moonshineConfig()->getHomeRoute();

        if (str_starts_with($route, 'moonshine.')) {
            return $this->router->to(
                str_replace('moonshine.', '', $route)
            );
        }


        return route($route);
    }
}
