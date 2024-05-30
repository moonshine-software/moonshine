<?php

namespace MoonShine\Laravel\Pages;

use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Components\SocialAuth;
use MoonShine\Laravel\Http\Controllers\ProfileController;
use MoonShine\Laravel\MoonShineAuth;
use MoonShine\Laravel\TypeCasts\ModelCast;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Components\Tabs\Tabs;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Text;

/**
 * @extends Page<Fields>
 */
class ProfilePage extends Page
{
    /**
     * @return array<string, string>
     */
    public function breadcrumbs(): array
    {
        return [
            '#' => $this->title(),
        ];
    }

    public function title(): string
    {
        return __('moonshine::ui.profile');
    }

    public function fields(): array
    {
        return [
            Box::make([
                Tabs::make([
                    Tab::make(__('moonshine::ui.resource.main_information'), [
                        ID::make()->sortable(),

                        Text::make(trans('moonshine::ui.resource.name'), 'name')
                            ->setValue(auth()->user()
                                ->{moonshineConfig()->getUserField('name')})
                            ->required(),

                        Text::make(trans('moonshine::ui.login.username'), 'username')
                            ->setValue(auth()->user()
                                ->{moonshineConfig()->getUserField('username', 'email')})
                            ->required(),

                        Image::make(trans('moonshine::ui.resource.avatar'), 'avatar')
                            ->setValue(auth()->user()
                                ->{moonshineConfig()->getUserField('avatar')} ?? null)
                            ->disk(moonshineConfig()->getDisk())
                            ->options(moonshineConfig()->getDiskOptions())
                            ->dir('moonshine_users')
                            ->removable()
                            ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif']),
                    ]),

                    Tab::make(trans('moonshine::ui.resource.password'), [
                        Heading::make(__('moonshine::ui.resource.change_password')),

                        Password::make(trans('moonshine::ui.resource.password'), 'password')
                            ->customAttributes(['autocomplete' => 'new-password'])
                            ->eye(),

                        PasswordRepeat::make(trans('moonshine::ui.resource.repeat_password'), 'password_repeat')
                            ->customAttributes(['autocomplete' => 'confirm-password'])
                            ->eye(),
                    ]),
                ]),
            ]),
        ];
    }

    public function components(): array
    {
        return [
            FormBuilder::make(action([ProfileController::class, 'store']))
                //->async()
                ->customAttributes([
                    'enctype' => 'multipart/form-data',
                ])
                ->fields($this->fields())
                ->cast(ModelCast::make(MoonShineAuth::model()::class))
                ->submit(__('moonshine::ui.save'), [
                    'class' => 'btn-lg btn-primary',
                ]),

            SocialAuth::make(profileMode: true),
        ];
    }
}
