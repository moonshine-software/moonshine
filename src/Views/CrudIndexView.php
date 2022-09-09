<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Views;

use Leeto\MoonShine\ActionsLayer\MakeTableAction;
use Leeto\MoonShine\Contracts\ViewComponentContract;
use Leeto\MoonShine\ViewComponents\ViewComponents;

final class CrudIndexView extends MoonShineView
{
    protected static string $component = 'CrudIndexView';

    public function components(): ViewComponents
    {
        return ViewComponents::make([
            $this->resolveTable()
        ]);
    }

    public function resolveTable(): ViewComponentContract
    {
        return (new MakeTableAction())($this->resource())
            ->endpointData([
                'resourceUri' => $this->resource()->uriKey(),
                'viewUri' => $this->uriKey(),
            ]);
    }
}
