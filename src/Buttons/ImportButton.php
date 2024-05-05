<?php

namespace MoonShine\Buttons;

use MoonShine\Components\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Fields\File;
use MoonShine\Handlers\ImportHandler;
use MoonShine\Resources\ModelResource;

final class ImportButton
{
    public static function for(ModelResource $resource, ImportHandler $import): ActionButton
    {
        return ActionButton::make(
            $import->getLabel(),
            '#'
        )
            ->success()
            ->icon($import->getIconValue(), $import->isCustomIcon(), $import->getIconPath())
            ->inOffCanvas(
                fn (): string => $import->getLabel(),
                fn (): FormBuilder => FormBuilder::make(
                    $resource->route('handler', query: ['handlerUri' => $import->uriKey()])
                )
                    ->fields([
                        File::make(column: $import->getInputName())->required(),
                    ])
                    ->submit(__('moonshine::ui.confirm')),
                name: 'import-off-canvas'
            );
    }
}
