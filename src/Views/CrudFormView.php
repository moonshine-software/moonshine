<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Views;

use Leeto\MoonShine\ActionsLayer\MakeFormAction;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\Contracts\ValueEntityContract;
use Leeto\MoonShine\Contracts\ViewComponentContract;
use Leeto\MoonShine\MoonShineRouter;
use Leeto\MoonShine\ViewComponents\ViewComponents;

final class CrudFormView extends MoonShineView
{
    protected static string $component = 'CrudFormView';

    final public function __construct(ResourceContract $resource, protected ?ValueEntityContract $value = null)
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
            $this->resolveForm()
        ]);
    }

    public function resolveForm(): ViewComponentContract
    {
        return (new MakeFormAction())(
            $this->resource(),
            $this->value
        )->endpointName('view-component.entity')->endpointData([
            'resourceUri' => $this->resource()->uriKey(),
            'viewUri' => $this->uriKey(),
            'id' => $this->value?->id()
        ]);
    }
}
