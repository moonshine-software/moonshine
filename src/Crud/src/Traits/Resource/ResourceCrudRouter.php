<?php

declare(strict_types=1);

namespace MoonShine\Crud\Traits\Resource;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Support\Enums\PageType;

/**
 * @template T
 * @mixin CrudResourceContract
 */
trait ResourceCrudRouter
{
    protected ?PageType $redirectAfterSave = null;

    /**
     * @param DataWrapperContract<T>|int|string|null $key
     * @param array<string, int|float|string|null> $query
     */
    public function getRoute(
        ?string $name = null,
        DataWrapperContract|int|string|null $key = null,
        array $query = []
    ): string {
        $key = $key instanceof DataWrapperContract ? $key->getKey() : $key;

        return $this->getRouter()->to(
            $name,
            filled($key) ? array_merge(['resourceItem' => $key], $query) : $query
        );
    }

    /**
     * @param class-string<PageContract>|PageContract $page
     * @param  array<string, string|int|float|null>  $params
     * @param  string|string[]|null  $fragment
     */
    public function getPageUrl(string|PageContract $page, array $params = [], null|string|array $fragment = null): string
    {
        return $this->getRouter()->getEndpoints()->toPage($page, $this, params: $params, extra: [
            'fragment' => $fragment,
        ]);
    }

    /**
     * @param  array<string, string|int|float|null>  $params
     * @param  string|string[]|null  $fragment
     *
     */
    public function getIndexPageUrl(array $params = [], null|string|array $fragment = null): string
    {
        return $this->getPageUrl($this->getIndexPage(), params: $params, fragment: $fragment);
    }

    /**
     * @param DataWrapperContract<T>|int|string|null $key
     * @param  array<string, string|int|float|bool|null>  $params
     * @param  string|string[]|null  $fragment
     *
     */
    public function getFormPageUrl(
        DataWrapperContract|int|string|null $key = null,
        array $params = [],
        null|string|array $fragment = null
    ): string {
        return $this->getPageUrl(
            $this->getFormPage(),
            params: array_filter([
                ...$params,
                ...['resourceItem' => $key instanceof DataWrapperContract ? $key->getKey() : $key],
            ], static fn (bool|float|int|string|null $value) => filled($value)),
            fragment: $fragment
        );
    }

    /**
     * @param DataWrapperContract<T>|int|string $key
     * @param  array<string, string|int|float|null>  $params
     * @param  string|string[]|null  $fragment
     *
     */
    public function getDetailPageUrl(
        DataWrapperContract|int|string $key,
        array $params = [],
        null|string|array $fragment = null
    ): string {
        return $this->getPageUrl(
            $this->getDetailPage(),
            params: array_filter([
                ...$params,
                ...['resourceItem' => $key instanceof DataWrapperContract ? $key->getKey() : $key],
            ], static fn (float|int|string|null $value) => filled($value)),
            fragment: $fragment
        );
    }

    /**
     * @param  string|string[]  $fragment
     * @param DataWrapperContract<T>|int|string|null $key
     * @param  array<string, string|int|float|null>  $params
     */
    public function getFragmentLoadUrl(
        string|array $fragment,
        ?PageContract $page = null,
        DataWrapperContract|int|string|null $key = null,
        array $params = []
    ): string {
        if (\is_null($page)) {
            $page = $this->getIndexPage();
        }

        return $this->getPageUrl(
            $page,
            params: array_filter([
                ...$params,
                ...['resourceItem' => $key instanceof DataWrapperContract ? $key->getKey() : $key],
            ], static fn (float|int|string|null $value) => filled($value)),
            fragment: $fragment
        );
    }

    /**
     * @param  array<string, string|int|float|null>  $params
     */
    public function getAsyncMethodUrl(
        string $method,
        ?string $message = null,
        array $params = [],
        ?PageContract $page = null,
    ): string {
        return $this->getRouter()->getEndpoints()->method(
            $method,
            $message,
            $params,
            page: $page ?? $this->getActivePage(),
        );
    }

    public function getRedirectAfterSave(): ?string
    {
        if (\is_null($this->redirectAfterSave) && ! $this->isAsync()) {
            $this->redirectAfterSave = PageType::FORM;
        }

        if (\is_null($this->redirectAfterSave)) {
            return null;
        }

        $params = \is_null($this->getItem()) || $this->redirectAfterSave === PageType::INDEX
            ? []
            : ['resourceItem' => $this->getCastedData()?->getKey()];

        return $this
            ->getPages()
            ->findByType($this->redirectAfterSave)
            ?->getRoute($params);
    }

    public function getRedirectAfterDelete(): string
    {
        return $this->getIndexPageUrl();
    }
}
