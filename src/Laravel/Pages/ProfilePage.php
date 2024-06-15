<?php

namespace MoonShine\Laravel\Pages;

use MoonShine\Core\Exceptions\MoonShineException;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Components\SocialAuth;
use MoonShine\Laravel\Http\Controllers\ProfileController;
use MoonShine\Laravel\MoonShineAuth;
use MoonShine\Laravel\TypeCasts\ModelCaster;
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

                        Text::make(trans('moonshine::ui.resource.name'), moonshineConfig()->getUserField('name'))
                            ->required(),

                        Text::make(trans('moonshine::ui.login.username'), moonshineConfig()->getUserField('username'))
                            ->required(),

                        Image::make(trans('moonshine::ui.resource.avatar'), moonshineConfig()->getUserField('avatar'))
                            ->disk(moonshineConfig()->getDisk())
                            ->options(moonshineConfig()->getDiskOptions())
                            ->dir('moonshine_users')
                            ->removable()
                            ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif']),
                    ]),

                    Tab::make(trans('moonshine::ui.resource.password'), [
                        Heading::make(__('moonshine::ui.resource.change_password')),

                        Password::make(trans('moonshine::ui.resource.password'), moonshineConfig()->getUserField('password'))
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
            $this->getForm(),
            SocialAuth::make(profileMode: true),
        ];
    }

    /**
     * @throws MoonShineException
     */
    public function getForm(): FormBuilder
    {
        $user = MoonShineAuth::guard()->user() ?? MoonShineAuth::model();

        if(is_null($user)) {
            throw new MoonShineException('Model is required');
        }

        return FormBuilder::make(action([ProfileController::class, 'store']))
            ->async()
            ->fields($this->fields())
            ->fillCast($user, new ModelCaster($user::class))
            ->submit(__('moonshine::ui.save'), [
                'class' => 'btn-lg btn-primary',
            ]);
    }
}
