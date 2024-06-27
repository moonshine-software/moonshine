<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Core\Contracts\PageContract;
use MoonShine\Core\Contracts\ResourceContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Support\Enums\PageType;

/**
 * @template-covariant TModel of Model
 * @mixin ResourceContract
 */
trait ResourceModelCrudRouter
{
    protected ?PageType $redirectAfterSave = PageType::FORM;

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

    public function getPageUrl(Page $page, array $params = [], ?string $fragment = null): string
    {
        return $this->getRouter()->getEndpoints()->toPage($page, params: $params, extra: [
            'fragment' => $fragment,
        ]);
    }

    public function getIndexPageUrl(array $params = [], ?string $fragment = null): string
    {
        return $this->getPageUrl($this->getIndexPage(), params: $params, fragment: $fragment);
    }


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


    public function getFragmentLoadUrl(
        string $fragment,
        ?PageContract $page = null,
        Model|int|string|null $model = null,
        array $params = []
    ): string {
        if(is_null($page)) {
            $page = $this->getIndexPage();
        }

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
        ?Page $page = null,
    ): string {
        return $this->getRouter()->getEndpoints()->asyncMethod(
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
