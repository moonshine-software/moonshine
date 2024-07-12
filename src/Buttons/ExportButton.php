<?php

namespace MoonShine\Buttons;

use Illuminate\Support\Arr;
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

        $button = ActionButton::make(
            $export->label(),
            trim("$url?ts=" . time() . "&$query", '&')
        )
            ->primary()
            ->customAttributes(['class' => '_change-query', 'data-original-url' => $url])
            ->icon($export->iconValue());

        if ($export->withConfirm()) {
            $button->withConfirm(content: trans('moonshine::ui.resource.export.confirm_content'));
        }

        return $button;
    }
}
