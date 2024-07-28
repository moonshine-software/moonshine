<?php

namespace MoonShine\Buttons;

use Illuminate\Support\Arr;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
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
        $ts = "ts=" . time();

        $attributes = [
            'class' => '_change-query',
            'data-original-url' => $url,
            'data-original-query' => $ts,
        ];

        $button = ActionButton::make(
            $export->label(),
            trim("$url?$ts&$query", '&')
        )
            ->primary()
            ->customAttributes($attributes)
            ->icon($export->iconValue());

        if ($export->isWithConfirm()) {
            $button->withConfirm(
                content: trans('moonshine::ui.resource.export.confirm_content'),
                formBuilder: static fn(FormBuilder $form) => $form->customAttributes($attributes)
            );
        }

        return $button;
    }
}
