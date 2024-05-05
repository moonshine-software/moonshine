<?php

namespace MoonShine\Buttons;

use Illuminate\Support\Arr;
use MoonShine\Components\ActionButtons\ActionButton;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Resources\ModelResource;

final class ExportButton
{
    public static function for(ModelResource $resource, ExportHandler $export): ActionButton
    {
        $query = Arr::query(request(['filters', 'sort', 'query-tag'], []));
        $url = $resource->route('handler', query: ['handlerUri' => $export->uriKey()]);

        return ActionButton::make(
            $export->getLabel(),
            $url . ($query ? '?' . $query : '')
        )
            ->primary()
            ->customAttributes(['class' => '_change-query', 'data-original-url' => $url])
            ->icon($export->getIconValue(), $export->isCustomIcon(), $export->getIconPath());
    }
}
