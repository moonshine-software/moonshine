<?php

namespace MoonShine\Pages;

use MoonShine\Forms\LoginForm;
use MoonShine\Layouts\LoginLayout;

class LoginPage extends Page
{
    protected ?string $layout = LoginLayout::class;

    public function components(): array
    {
        $form = $this->getForm();

        return [
            $form(),
        ];
    }

    private function getForm(): object
    {
        return new (config('moonshine.forms.login', LoginForm::class));
    }
}
