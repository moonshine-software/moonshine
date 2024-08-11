<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Support\Enums\PageType;

/**
 * @template-covariant TModel of Model
 * @mixin ResourceContract
 */
trait ResourceModelCrudRouter
{
    protected ?PageType $redirectAfterSave = PageType::FORM;

    /**
     * @param TModel|int|string|null $key
     */
    public function getRoute(
        string $name = null,
        Model|int|string|null $key = null,
        array $query = []
    ): string {
        $key = $key instanceof Model ? $key->getKey() : $key;

        return $this->getRouter()->to(
            $name,
            filled($key) ? array_merge(['resourceItem' => $key], $query) : $query
        );
    }

    public function getPageUrl(PageContract $page, array $params = [], ?string $fragment = null): string
    {
        return $this->getRouter()->getEndpoints()->toPage($page, params: $params, extra: [
            'fragment' => $fragment,
        ]);
    }

    public function getIndexPageUrl(array $params = [], ?string $fragment = null): string
    {
        return $this->getPageUrl($this->getIndexPage(), params: $params, fragment: $fragment);
    }

    /**
     * @param TModel|int|string|null $model
     */
    public function getFormPageUrl(
        Model|int|string|null $model = null,
        array $params = [],
        ?string $fragment = null
    ): string {
        return $this->getPageUrl(
            $this->getFormPage(),
            params: array_filter([
                ...$params,
                ...['resourceItem' => $model instanceof Model ? $model->getKey() : $model],
            ], static fn ($value) => filled($value)),
            fragment: $fragment
        );
    }

    /**
     * @param TModel|int|string $model
     */
    public function getDetailPageUrl(
        Model|int|string $model,
        array $params = [],
        ?string $fragment = null
    ): string {
        return $this->getPageUrl(
            $this->getDetailPage(),
            params: array_filter([
                ...$params,
                ...['resourceItem' => $model instanceof Model ? $model->getKey() : $model],
            ], static fn ($value) => filled($value)),
            fragment: $fragment
        );
    }

    /**
     * @param TModel|int|string|null $model
     */
    public function getFragmentLoadUrl(
        string $fragment,
        PageContract $page,
        Model|int|string|null $model,
        array $params = []
    ): string {
        return $this->getPageUrl(
            $page,
            params: array_filter([
                ...$params,
                ...['resourceItem' => $model instanceof Model ? $model->getKey() : $model],
            ], static fn ($value) => filled($value)),
            fragment: $fragment
        );
    }

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
            page: $page,
        );
    }

    public function getRedirectAfterSave(): string
    {
        $params = is_null($this->getItem()) || $this->redirectAfterSave === PageType::INDEX
            ? []
            : ['resourceItem' => $this->getItem()?->getKey()];

        if (! is_null($this->redirectAfterSave)) {
            return $this
                ->getPages()
                ->findByType($this->redirectAfterSave)
                ?->getRoute($params);
        }

        return $this->getFormPageUrl(params: $params);
    }

    public function getRedirectAfterDelete(): string
    {
        return $this->getIndexPageUrl();
    }
}
