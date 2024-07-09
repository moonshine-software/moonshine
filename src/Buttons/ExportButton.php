<?php

namespace MoonShine\Buttons;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Resources\ModelResource;

final class ExportButton
{
    public static function for(ModelResource $resource, ExportHandler $export): ActionButton
    {
        $query = Arr::query(
            request()->only(['filters', 'sort', 'query-tag'])
        );

        $url = $resource->route('handler', query: ['handlerUri' => $export->uriKey()]);

        return ActionButton::make(
            $export->label(),
            "{$url}?hash=" . Str::random(8) . ($query ? "&{$query}" : '')
        )
            ->primary()
            ->customAttributes(['class' => '_change-query', 'data-original-url' => $url])
            ->icon($export->iconValue());
    }
}
