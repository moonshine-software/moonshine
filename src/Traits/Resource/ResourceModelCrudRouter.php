<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Enums\PageType;
use MoonShine\MoonShineRouter;

/**
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

    public function route(
        string $name = null,
        int|string $key = null,
        array $query = []
    ): string {
        $query['resourceUri'] = $this->uriKey();

        data_forget($query, ['change-moonshine-locale', 'reset']);

        return MoonShineRouter::to(
            $name,
            $key ? array_merge(['resourceItem' => $key], $query) : $query
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

        return to_page(
            page: $this->formPage(),
            resource: $this,
            params: $params
        );
    }

    public function redirectAfterDelete(): string
    {
        return to_page(page: $this->indexPage(), resource: $this);
    }
}
