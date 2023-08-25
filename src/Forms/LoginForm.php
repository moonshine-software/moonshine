<?php

declare(strict_types=1);

namespace MoonShine\Forms;

use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Checkbox;
use MoonShine\Fields\Password;
use MoonShine\Fields\Text;

final class LoginForm
{
    public function __invoke()
    {
        return FormBuilder::make()
            ->customAttributes([
                'class' => 'authentication-form',
            ])
            ->action(route('moonshine.authenticate'))
            ->fields([
                Text::make(__('moonshine::ui.login.username'), 'username')
                    ->required()
                    ->customAttributes([
                        'autofocus' => true,
                        'autocomplete' => 'username',
                    ]),

                Password::make(__('moonshine::ui.login.password'), 'password')
                    ->required(),

                Checkbox::make(__('moonshine::ui.login.remember_me'), 'remember'),
            ])->submit(__('moonshine::ui.login.login'), [
                'class' => 'btn-primary btn-lg w-full',
            ]);
    }
}
