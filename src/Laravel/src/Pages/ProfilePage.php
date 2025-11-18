<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Crud\Traits\WithComponentsPusher;
use MoonShine\Laravel\Http\Controllers\ProfileController;
use MoonShine\Laravel\MoonShineAuth;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\MenuManager\Attributes\SkipMenu;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Text;

#[SkipMenu]
class ProfilePage extends Page
{
    use WithComponentsPusher;

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle(),
        ];
    }

    public function getTitle(): string
    {
        return __('moonshine::ui.profile');
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        $userFields = array_filter([
            ID::make(),

            moonshineConfig()->getUserField('name')
                ? Text::make(__('moonshine::ui.resource.name'), moonshineConfig()->getUserField('name'))
                ->required()
                : null,

            moonshineConfig()->getUserField('username')
                ? Text::make(__('moonshine::ui.login.username'), moonshineConfig()->getUserField('username'))
                ->required()
                : null,

            moonshineConfig()->getUserField('avatar')
                ? Image::make(__('moonshine::ui.resource.avatar'), moonshineConfig()->getUserField('avatar'))
                ->disk(moonshineConfig()->getDisk())
                ->options(moonshineConfig()->getDiskOptions())
                ->dir(moonshineConfig()->getUserAvatarsDir())
                ->removable()
                ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif'])
                : null,
        ]);

        $userPasswordsFields = moonshineConfig()->getUserField('password') ? [
            Heading::make(__('moonshine::ui.resource.change_password')),

            Password::make(__('moonshine::ui.resource.password'), moonshineConfig()->getUserField('password'))
                ->customAttributes(['autocomplete' => 'new-password'])
                ->eye(),

            PasswordRepeat::make(__('moonshine::ui.resource.repeat_password'), 'password_repeat')
                ->customAttributes(['autocomplete' => 'confirm-password'])
                ->eye(),
        ] : [];

        return [
            Box::make([
                Tabs::make([
                    Tab::make(__('moonshine::ui.resource.main_information'), $userFields),
                    Tab::make(__('moonshine::ui.resource.password'), $userPasswordsFields)->canSee(
                        fn (): bool => $userPasswordsFields !== [],
                    ),
                ]),
            ]),
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        return [
            $this->getForm(),
            ...$this->getPushedComponents(),
        ];
    }

    public function getForm(): FormBuilderContract
    {
        $user = MoonShineAuth::getGuard()->user() ?? MoonShineAuth::getModel();

        return FormBuilder::make(action([ProfileController::class, 'store']))
            ->async()
            ->fields($this->fields())
            ->fillCast($user, new ModelCaster($user::class))
            ->submit(__('moonshine::ui.save'), [
                'class' => 'btn-primary btn-lg',
            ]);
    }
}
