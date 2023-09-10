<?php

namespace MoonShine\Buttons\IndexPage;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Resources\ModelResource;

final class ExportButton
{
    public static function for(ModelResource $resource, ExportHandler $export): ActionButton
    {
        return ActionButton::make(
            $export->label(),
            $resource->route('handler', query: ['handlerUri' => $export->uriKey()])
        )
            ->customAttributes(['class' => 'btn btn-primary'])
            ->icon($export->iconValue());
    }
}
