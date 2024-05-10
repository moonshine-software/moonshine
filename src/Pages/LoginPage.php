<?php

namespace MoonShine\Pages;

use MoonShine\Forms\LoginForm;
use MoonShine\Layouts\LoginLayout;

class LoginPage extends Page
{
    protected ?string $layout = LoginLayout::class;

    public function components(): array
    {
        return [
            moonshineConfig()->getForm('login', LoginForm::class),
        ];
    }
}
