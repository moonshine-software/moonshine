<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Core\Contracts\ResourceContract;
use MoonShine\Core\Pages\Page;
use MoonShine\Support\Enums\PageType;

/**
 * @template-covariant TModel of Model
 * @mixin ResourceContract
 */
trait ResourceModelCrudRouter
{
    protected ?PageType $redirectAfterSave = PageType::FORM;

    /**
     * @param Model|int|string|null $key
     */
    public function route(
        string $name = null,
        Model|int|string|null $key = null,
        array $query = []
    ): string {
        $key = $key instanceof Model ? $key->getKey() : $key;

        return $this->router()->to(
            $name,
            filled($key) ? array_merge(['resourceItem' => $key], $query) : $query
        );
    }

    public function pageUrl(Page $page, array $params = [], ?string $fragment = null): string
    {
        return $this->router()->getEndpoints()->toPage($page, params: $params, extra: [
            'fragment' => $fragment
        ]);
    }

    public function indexPageUrl(array $params = [], ?string $fragment = null): string
    {
        return $this->pageUrl($this->indexPage(), params: $params, fragment: $fragment);
    }

    /**
     * @param Model|int|string|null $model
     *
     */
    public function formPageUrl(
        Model|int|string|null $model = null,
        array $params = [],
        ?string $fragment = null
    ): string {
        return $this->pageUrl(
            $this->formPage(),
            params: array_filter([
                ...$params,
                ...['resourceItem' => $model instanceof Model ? $model->getKey() : $model],
            ], static fn ($value) => filled($value)),
            fragment: $fragment
        );
    }

    /**
     * @param Model|int|string $model
     *
     */
    public function detailPageUrl(
        Model|int|string $model,
        array $params = [],
        ?string $fragment = null
    ): string {
        return $this->pageUrl(
            $this->detailPage(),
            params: array_filter([
                ...$params,
                ...['resourceItem' => $model instanceof Model ? $model->getKey() : $model],
            ], static fn ($value) => filled($value)),
            fragment: $fragment
        );
    }

    /**
     * @param Model|int|string|null $model
     *
     */
    public function fragmentLoadUrl(
        string $fragment,
        Page $page,
        Model|int|string|null $model,
        array $params = []
    ): string {
        return $this->pageUrl(
            $page,
            params: array_filter([
                ...$params,
                ...['resourceItem' => $model instanceof Model ? $model->getKey() : $model],
            ], static fn ($value) => filled($value)),
            fragment: $fragment
        );
    }

    public function asyncMethodUrl(
        string $method,
        ?string $message = null,
        array $params = [],
        ?Page $page = null,
    ): string {
        return $this->router()->getEndpoints()->asyncMethod(
            $method,
            $message,
            $params,
            page: $page,
        );
    }

    public function redirectAfterSave(): string
    {
        $params = is_null($this->getItem()) || $this->redirectAfterSave === PageType::INDEX
            ? []
            : ['resourceItem' => $this->getItem()?->getKey()];

        if (! is_null($this->redirectAfterSave)) {
            return $this
                ->getPages()
                ->findByType($this->redirectAfterSave)
                ?->route($params);
        }

        return $this->formPageUrl(params: $params);
    }

    public function redirectAfterDelete(): string
    {
        return $this->indexPageUrl();
    }
}
