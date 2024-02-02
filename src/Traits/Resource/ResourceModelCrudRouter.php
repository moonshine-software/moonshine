<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Enums\PageType;
use MoonShine\Pages\Page;

/**
 * @template TModel of Model
 * @mixin ResourceContract
 */
trait ResourceModelCrudRouter
{
    protected ?PageType $redirectAfterSave = PageType::FORM;

    public function currentRoute(array $query = []): string
    {
        return str(request()->url())->when(
            $query,
            static fn (Stringable $str): Stringable => $str
                ->append('?')
                ->append(Arr::query($query))
        )->value();
    }

    /**
     * @param string|null $name
     * @param TModel|int|string|null $key
     * @param array $query
     *
     * @return string
     */
    public function route(
        string $name = null,
        Model|int|string|null $key = null,
        array $query = []
    ): string {
        $query['resourceUri'] = $this->uriKey();

        data_forget($query, ['change-moonshine-locale', 'reset']);

        $key = $key instanceof Model ? $key->getKey() : $key;

        return moonshineRouter()->to(
            $name,
            filled($key) ? array_merge(['resourceItem' => $key], $query) : $query
        );
    }

    public function pageUrl(Page $page, array $params = [], ?string $fragment = null): string
    {
        return moonshineRouter()->to_page(
            $page,
            $this,
            params: $params,
            fragment: $fragment
        );
    }

    public function indexPageUrl(array $params = [], ?string $fragment = null): string
    {
        return $this->pageUrl($this->indexPage(), params: $params, fragment: $fragment);
    }

    /**
     * @param TModel|int|string|null $model
     * @param array $params
     * @param string|null $fragment
     *
     * @return string
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
     * @param TModel|int|string $model
     * @param array $params
     * @param string|null $fragment
     *
     * @return string
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
     * @param string $fragment
     * @param Page $page
     * @param TModel|int|string|null $model
     * @param array $params
     *
     * @return string
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
        return moonshineRouter()->asyncMethod(
            $method,
            $message,
            $params,
            page: $page,
            resource: $this
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
                ->route($params);
        }

        return $this->formPageUrl(params: $params);
    }

    public function redirectAfterDelete(): string
    {
        return $this->indexPageUrl();
    }
}
