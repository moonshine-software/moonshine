<?php

namespace MoonShine\Buttons\IndexPage;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Fields\File;
use MoonShine\Handlers\ImportHandler;
use MoonShine\Resources\ModelResource;

final class ImportButton
{
    public static function for(ModelResource $resource, ImportHandler $import): ActionButton
    {
        return ActionButton::make(
            $import->label(),
            '#'
        )
            ->success()
            ->icon($import->iconValue())
            ->inOffCanvas(
                fn (): string => $import->label(),
                fn (): FormBuilder => FormBuilder::make(
                    $resource->route('handler', query: ['handlerUri' => $import->uriKey()])
                )
                    ->fields([
                        File::make(column: $import->getInputName())->required(),
                    ])
                    ->submit(__('moonshine::ui.confirm'))
            );
    }
}
