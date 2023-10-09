<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\MoonShineRouter;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;

/**
 * @mixin ResourceContract
 */
trait ResourceModelCrudRouter
{
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
        return request('_redirect') ?? to_page(
            page: FormPage::class,
            resource: $this,
            params: is_null($this->getItem()) ?: ['resourceItem' => $this->getItem()->getKey()]
        );
    }

    public function redirectAfterDelete(): string
    {
        return request('_redirect') ?? to_page(page: IndexPage::class, resource: $this);
    }
}
