<?php

namespace MoonShine\Pages;

use MoonShine\Components\FormBuilder;
use MoonShine\Decorations\TextBlock;
use MoonShine\Http\Controllers\ProfileController;
use MoonShine\MoonShineAuth;
use MoonShine\Resources\ModelResource;
use MoonShine\TypeCasts\ModelCast;

/**
 * @method ModelResource getResource()
 */
class ProfilePage extends Page
{
    public function components(): array
    {
        return [
            FormBuilder::make(action([ProfileController::class, 'store']))
                ->async()
                ->customAttributes([
                    'enctype' => 'multipart/form-data',
                ])
                ->fields(
                    $this->getResource()
                        ->getFields()
                        ->toArray()
                )
                ->cast(ModelCast::make(MoonShineAuth::model()::class))
                ->submit(__('moonshine::ui.save'), [
                    'class' => 'btn-lg btn-primary',
                ]),

            TextBlock::make(
                '',
                view('moonshine::ui.social-auth', [
                    'title' => trans('moonshine::ui.resource.link_socialite'),
                    'attached' => true,
                ])->render()
            ),
        ];
    }
}
