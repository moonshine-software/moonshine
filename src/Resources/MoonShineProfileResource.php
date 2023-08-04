<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use Illuminate\Validation\Rule;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Heading;
use MoonShine\Decorations\Tab;
use MoonShine\Decorations\Tabs;
use MoonShine\Fields\ID;
use MoonShine\Fields\Image;
use MoonShine\Fields\Password;
use MoonShine\Fields\PasswordRepeat;
use MoonShine\Fields\Text;
use MoonShine\Models\MoonshineUser;
use MoonShine\Pages\ProfilePage;

class MoonShineProfileResource extends ModelResource
{
    public string $model = MoonshineUser::class;

    public function title(): string
    {
        return __('moonshine::ui.profile');
    }

    public function fields(): array
    {
        return [
            Block::make([
                Tabs::make([
                    Tab::make('Main', [
                        ID::make()
                            ->sortable()
                            ->showOnExport(),

                        Text::make(trans('moonshine::ui.resource.name'), 'name')
                            ->setValue(auth()->user()
                                ->{config('moonshine.auth.fields.name', 'name')})
                            ->required(),

                        Text::make(trans('moonshine::ui.login.username'), 'username')
                            ->setValue(auth()->user()
                                ->{config('moonshine.auth.fields.username', 'email')})
                            ->required(),

                        Image::make(trans('moonshine::ui.resource.avatar'), 'avatar')
                            ->setValue(auth()->user()
                                ->{config('moonshine.auth.fields.avatar', 'avatar')} ?? null)
                            ->disk('public')
                            ->dir('moonshine_users')
                            ->removable()
                            ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif']),
                    ]),

                    Tab::make(trans('moonshine::ui.resource.password'), [
                        Heading::make('Change password'),

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

    public function rules($item): array
    {
        return [
            'name' => 'required',
            config('moonshine.auth.fields.username', 'email') => [
                'sometimes',
                'bail',
                'required',
                Rule::unique('moonshine_users')->ignoreModel($item),
            ],
            'password' => $item->exists
                ? 'sometimes|nullable|min:6|required_with:password_repeat|same:password_repeat'
                : 'required|min:6|required_with:password_repeat|same:password_repeat',
        ];
    }

    public function pages(): array
    {
        return [
            ProfilePage::make($this->title()),
        ];
    }
}
