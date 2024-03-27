<?php

namespace MoonShine\Pages;

use MoonShine\Components\FlexibleRender;
use MoonShine\Components\FormBuilder;
use MoonShine\Decorations\Heading;
use MoonShine\Decorations\Tab;
use MoonShine\Decorations\Tabs;
use MoonShine\Fields\ID;
use MoonShine\Fields\Image;
use MoonShine\Fields\Password;
use MoonShine\Fields\PasswordRepeat;
use MoonShine\Fields\Text;
use MoonShine\Forms\LoginForm;
use MoonShine\Http\Controllers\ProfileController;
use MoonShine\Layouts\LoginLayout;
use MoonShine\MoonShineAuth;
use MoonShine\TypeCasts\ModelCast;

class LoginPage extends Page
{
    protected string $layout = LoginLayout::class;

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
