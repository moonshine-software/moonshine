<?php

declare(strict_types=1);

namespace MoonShine\Forms;

use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Password;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\Text;

final class LoginForm
{
    public function __invoke(): FormBuilder
    {
        return FormBuilder::make()
            ->customAttributes([
                'class' => 'authentication-form',
            ])
            ->action(moonshineRouter()->to('authenticate'))
            ->fields([
                Text::make(__('moonshine::ui.login.username'), 'username')
                    ->required()
                    ->customAttributes([
                        'autofocus' => true,
                        'autocomplete' => 'username',
                    ]),

                Password::make(__('moonshine::ui.login.password'), 'password')
                    ->required(),

                Switcher::make(__('moonshine::ui.login.remember_me'), 'remember'),
            ])->submit(__('moonshine::ui.login.login'), [
                'class' => 'btn-primary btn-lg w-full',
            ]);
    }
}
