<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Core\Attributes\Layout;
use MoonShine\Crud\Forms\LoginForm;
use MoonShine\Laravel\Layouts\LoginLayout;
use MoonShine\MenuManager\Attributes\SkipMenu;

#[SkipMenu]
#[Layout(LoginLayout::class)]
class LoginPage extends Page
{
    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        return [
            $this->getCore()->getConfig()->getForm(
                'login',
                LoginForm::class,
                action: $this->getRouter()->to('authenticate'),
                core: $this->getCore()
            ),
        ];
    }
}
