<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Views;

use Leeto\MoonShine\ActionsLayer\MakeDetailCardAction;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\Contracts\EntityContract;
use Leeto\MoonShine\Contracts\ViewComponentContract;
use Leeto\MoonShine\MoonShineRouter;
use Leeto\MoonShine\ViewComponents\ViewComponents;

final class CrudDetailView extends MoonShineView
{
    protected static string $component = 'CrudDetailView';

    final public function __construct(ResourceContract $resource, protected EntityContract $value)
    {
        parent::__construct($resource);

        $this->resource = $resource;
    }

    public function endpoint(): string
    {
        return MoonShineRouter::to(
            'view.entity',
            [
                'resourceUri' => $this->resource()->uriKey(),
                'viewUri' => $this->uriKey(),
                'id' => $this->value?->id()
            ]
        );
    }

    public function components(): ViewComponents
    {
        return ViewComponents::make([
            $this->resolveDetailCard(),
        ]);
    }

    public function resolveDetailCard(): ViewComponentContract
    {
        return (new MakeDetailCardAction())(
            $this->resource(),
            $this->value
        )->endpointName('view-component.entity')->endpointData([
            'resourceUri' => $this->resource()->uriKey(),
            'viewUri' => $this->uriKey(),
            'id' => $this->value?->id()
        ]);
    }
}
