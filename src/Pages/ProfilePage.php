<?php

namespace MoonShine\Pages;

use MoonShine\Components\FormBuilder;
use MoonShine\Http\Controllers\ProfileController;

class ProfilePage extends Page
{
    public function components(): array
    {
        return [
            FormBuilder::make(action([ProfileController::class, 'store']))
                ->customAttributes([
                    'enctype' => 'multipart/form-data',
                ])
                ->fields(
                    $this->getResource()
                        ->getFields()
                        ->toArray()
                )
                ->cast($this->getResource()->getModel()::class)
                ->submit(__('moonshine::ui.save'), [
                    'class' => 'btn-lg btn-primary',
                ]),
        ];
    }
}
